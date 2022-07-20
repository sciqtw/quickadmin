<?php
declare (strict_types=1);

namespace quick\admin\actions;


use quick\admin\annotation\AdminAuth;
use quick\admin\form\Form;
use quick\admin\http\model\Model;
use quick\admin\http\response\JsonResponse;
use think\facade\Db;

/**
 * @AdminAuth(auth=true,menu=true,login=true,title="Add")
 * @package quick\actions
 */
class AddAction extends RowAction
{

    public $name = "添加";

    /**
     * 初始化
     * @return $this
     */
    protected function init()
    {
        $this->display(function($display){
            $display->type("primary");
        });
        $this->dialog();

        return $this;
    }

    protected function initAction()
    {
//        $this->dialog();
//        $this->getDisplay()->type("text");
        return $this;
    }


//    protected function initAction()
//    {
//
//        $this->dialog();
//        return $this;
//    }


    /**
     * 动作异步数据接口
     *
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     */
    public function load()
    {

        if (!$this->handleCanRun($this->request, [])) {
            quick_abort(500, '你无权访问');
        }

        /** @var Form $form */
        $form = $this->form();
        $form->resolve([]);
        $form->url($this->storeUrl());
        $form->style("background-color", '#FFFFFF');
        $form->fixedFooter();
        $form->hideReset();
        $form = $this->resolveComponent($form);

        return $this->response()->success("success", $form);
    }


    /**
     * 动作提交数据接口
     *
     * @return bool|mixed|JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function store()
    {

        /** @var Model $model */
        $model = $this->getModel();

        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        $form = $this->form();
        $data = (array)$form->getSubmitData($this->request, 1);
        Db::startTrans();
        try {
            if ($this->beforeSavingCallback instanceof \Closure) {
                $beforeSavingCallback = \Closure::bind($this->beforeSavingCallback,$this);
                $data = call_user_func($beforeSavingCallback, $data, $this->request);
            }

            $res = $model->save($data);
            if (!$res) {
//                print_r($data);
                throw new \Exception("添加失败：".$model->getErrorMsg());
            }

            if ($this->afterSavingCallback instanceof \Closure) {
                $afterSavingCallback = \Closure::bind($this->afterSavingCallback,$this);
                $res = call_user_func($afterSavingCallback, $model->toArray(), $this->request);
                if ($res === false) {
                    throw new \Exception("添加失败");
                }
            }

            if ($res instanceof JsonResponse) {
                $response = $res;
            } else {
                $response = $this->response()->message("添加成功");
            }

            if ($this->isPage()) {
                $response->push($this->backUrl("index"));
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $response = $this->response()->error($e->getMessage(),[
                'line' => $e->getLine(),
                'trace' => $e->getTrace(),
            ]);
        }


        return $response;
    }






}
