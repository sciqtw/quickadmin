<?php

namespace app\admin\quick\actions;


use app\common\service\common\BuildGroupViewService;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\form\Form;
use think\Request;

/**
 * 编辑数据组
 * @AdminAuth(auth=true,menu=true,login=true,title="删除数据")
 * @package app\admin\resource\example\actions
 */
class GroupEditAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemGroup";


    protected function initAction()
    {
        $this->getDisplay()->type('text')->size('small');
        $this->dialog();
    }



    public function form()
    {
        $form = BuildGroupViewService::editGroupForm();
        return $form;
    }

    public function resolve($request, $model)
    {
        $form = $this->form();
        $form->url($this->storeUrl([
            self::$keyName => $request->param(self::$keyName)
        ]));
        $form->resolve($model);

        return $this->response()->success("success", $form);
    }

    public function handle($model, Request $request)
    {
        $form = $this->form();
        $data = (array)$form->getSubmitData($request, 3);
        $model->save($data);
        if (true) {
            $response = $this->response()->success("设置成功")->event('refresh',[],0,true);
        } else {
            $response = $this->response()->error("设置失败");
        }
        return $response;
    }


}
