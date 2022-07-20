<?php
declare (strict_types = 1);

namespace quick\admin\actions;



use Closure;
use quick\admin\form\Form;

/**
 * 行动作
 * Class RowAction
 * @package quick\actions
 */
abstract class RowAction extends Action
{

    /**
     * @var Form
     */
    protected $form;


    /**
     * 数据持久化之前回调.
     *
     * @var \Closure
     */
    protected $beforeSavingCallback;

    /**
     * 数据持久化之后回调.
     *
     * @var \Closure
     */
    protected $afterSavingCallback;

    /**
     * 数据加载之前.
     *
     * @var \Closure
     */
    protected $beforeLoadCallback;


    /**
     * 初始化
     * @return $this
     */
    protected function init()
    {
        $this->dialog();
        $this->getDisplay()->type("text");
        return $this;
    }




    /**
     * @param array $data
     * @return Action
     */
    public function data(array $data)
    {

        if(!empty($data[static::$pk])){
            $this->param([
                static::$keyName => $data[static::$pk]
            ]);
        }
        return parent::data($data);
    }


    /**
     *
     * @return mixed
     */
    public function getPkValue()
    {
        return $this->request->param(static::$keyName);
    }



    /**
     * @return mixed
     * @throws \think\Exception
     */
    protected function findModel()
    {
        [$pk,$pkValue] = [static::$pk,$this->getPkValue()];
        return $this->getModel()->where($pk,$pkValue)->find();
    }


    /**
     * 动作异步数据接口
     * @return mixed
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function load()
    {
        $model = $this->findModel();
        if (!$model) {
            quick_abort(500, '资源不存在');
        }
//        if (!$this->handleCanRun($this->request, $model)) {
//            quick_abort(500, '你无权访问');
//        }

        if ($this->beforeLoadCallback instanceof \Closure) {
            $beforeSavingCallback = \Closure::bind($this->beforeLoadCallback,$this);
            $model = call_user_func($beforeSavingCallback, $model, $this->request);
        }

        return $this->resolve($this->request,$model);
    }


    /**
     * 动作提交数据接口
     *
     * @return mixed
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function store()
    {
        $model = $this->findModel();
        if (!$model) {
            quick_abort(500, '资源不存在');
        }
//        if (!$this->handleCanRun($this->request, $model)) {
//            quick_abort(500, '你无权访问');
//        }
        return $this->handle($model, $this->request);
    }


    /**
     * 定义动作处理逻辑回调
     *
     * @param callable $beforeSavingCallback
     * @return $this
     */
    public function beforeSaveUsing(callable $beforeSavingCallback)
    {
        $this->beforeSavingCallback = $beforeSavingCallback;

        return $this;
    }


    /**
     * 定义动作处理逻辑回调
     *
     * @param callable $afterSavingCallback
     * @return $this
     */
    public function afterSaveUsing(callable $afterSavingCallback)
    {
        $this->afterSavingCallback = $afterSavingCallback;

        return $this;
    }

    /**
     * 数据加载之前回调
     *
     * @param callable $beforeLoadCallback
     * @return $this
     */
    public function beforeLoadUsing(callable $beforeLoadCallback)
    {
        $this->beforeLoadCallback = $beforeLoadCallback;

        return $this;
    }

    /**
     * @return Form
     */
    public function form()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    public function isPage()
    {
        if($this->panelComponent){
            return true;
        }
        return false;
    }

}
