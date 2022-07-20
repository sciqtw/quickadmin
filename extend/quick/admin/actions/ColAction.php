<?php
declare (strict_types = 1);

namespace quick\admin\actions;


/**
 * 行动作
 * Class RowAction
 * @package quick\actions
 */
abstract class ColAction extends Action
{


    public function __construct($name = '')
    {
        parent::__construct($name);
    }

    /**
     * 动作异步数据接口
     *
     * @return mixed
     */
    public function load()
    {
        [$pk,$pkValue] = [self::$pk,$this->getPkValue()];
        $model = self::newModel()->where($pk,$pkValue)->find();

        if (!$model) {
            quick_abort(500, '资源不存在1');
        }
        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        return $this->resolve($model, $this->request);
    }

    /**
     *
     * @return mixed
     */
    public function getPkValue()
    {
        return $this->request->param(self::$keyName);
    }

    /**
     * 动作提交数据接口
     *
     * @return mixed
     */
    public function store()
    {
        [$pk,$pkValue] = [self::$pk,$this->request->param(self::$keyName)];
        $model = self::newModel()->where($pk,$pkValue)->find();
        if (!$model) {
            quick_abort(500, '资源不存在');
        }
        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        return $this->handle($model, $this->request);
    }


    /**
     * 动作异步数据接口url
     * @return mixed|string
     */
    public function loadUrl()
    {

        $module = str_replace('.', '/', app('http')->getName());
        return $module . '/resource/' .app()->request->route('resource'). "/show";
    }

    /**
     * 动作提交接口url
     *
     * @return mixed|string
     */
    public function storeUrl()
    {
        $module = str_replace('.', '/', app('http')->getName());
        return $module . '/resource/' . app()->request->route('resource'). "/update";
    }

}
