<?php

namespace app\admin\quick\actions;


use quick\admin\actions\Action;
use quick\admin\annotation\AdminAuth;
use quick\admin\form\Form;
use quick\admin\http\response\JsonResponse;

/**
 * Class ConfigGroupEditAction
 * @AdminAuth(title="编辑配置分组",auth=true,login=true,menu=false)
 * @package app\admin\quick\actions
 */
class ConfigGroupEditAction extends Action
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemConfigGroup";


    protected function initAction()
    {
        $this->getDisplay()->type('text');

        $this->dialog();
    }


    public function form()
    {

        $form = Form::make();
        $form->text('title', '分组别名')->rules('require');
        $form->text('group', '变量名称')->rules('alphaDash')->rules('require');
        $form->text('sort', '排序')->number()->default(100);
        return $form;
    }


    /**
     * @return ConfigGroupEditAction
     */
    public function isEdit()
    {
        return $this->param(['type' => 'edit']);
    }


    /**
     * @return mixed|JsonResponse
     * @throws \think\Exception
     */
    public function load()
    {

//        if (!$this->handleCanRun($this->request, [])) {
//            quick_abort(500, '你无权访问');
//        }

        $model = $this->getModel();
        $data = $model->where([
            'id' => $this->request->post('id/d')
        ])->find();
        if (!$data) {
            return $this->response()->error("数据有误");
        }

        $form = $this->form();
        $form->resolve($data);
        $form->extendData(['id' => $this->request->post('id/d')]);
        $form->url($this->storeUrl());
        $form = $this->resolveComponent($form);
        return $this->response()->success("success", $form);
    }


    /**
     * 动作提交数据接口
     *
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function store()
    {

        $model = $this->getModel()->where([
            'id' => $this->request->post('id/d')
        ])->find();

        if (!$model) {
            return $this->response()->error('数据有误');
        }

        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        $form = $this->form();
        $data = (array)$form->getSubmitData($this->request, 1);

        try {

            $res = $model->save($data);
            if (!$res) {
                throw new \Exception("编辑失败");
            }
            return $this->response()->success("编辑成功")
                ->event('refresh', [], 0)
                ->message("编辑成功");

        } catch (\Exception $e) {
            return $this->response()->error($e->getMessage());
        }

    }


}
