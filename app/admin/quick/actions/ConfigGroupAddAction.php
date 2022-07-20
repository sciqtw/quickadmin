<?php

namespace app\admin\quick\actions;


use quick\admin\http\response\JsonResponse;
use quick\admin\annotation\AdminAuth;
use quick\admin\actions\Action;
use quick\admin\form\Form;

/**
 * Class ConfigGroupAddAction
 * @AdminAuth(title="添加配置分组",auth=true,login=true,menu=false)
 * @package app\admin\quick\actions
 */
class ConfigGroupAddAction extends Action
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
        $form->text('title','分组别名')->rules('require');
        $form->text('group', '变量名称')->rules('alphaDash')->rules('require')
            ->creationRules('unique:SystemConfigGroup');
        $form->text('sort', '排序')->number()->default(100);
        return $form;
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
        $parent_id = $this->request->post('id/d',0);
        $form = $this->form();
        $form->resolve([]);
        $form->extendData(['parent_id' => $parent_id]);
        $form->url($this->storeUrl());
        $form->style("background-color", '#FFFFFF');
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
        $model = $this->getModel();

        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        $form = $this->form();
        $data = (array)$form->getSubmitData($this->request, 1);

        try {
            $data['parent_id'] = $this->request->post('parent_id/d',0);
            $res = $model->create($data);
            if (!$res->id) {
                throw new \Exception("添加失败:".$res->getError());
            }

            return  $this->response()
                ->success("添加成功")
                ->event('refresh',[],0)
                ->message("添加成功");

        } catch (\Exception $e) {
            return  $this->response()->error($e->getMessage());
        }

    }


}
