<?php

namespace components\admin\src\actions;


use quick\admin\actions\Action;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\NodeService;
use quick\admin\library\service\QueueService;
use think\Request;

/**
 * 刷新权限
 * @AdminAuth(title="刷新权限",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class RefreshNodeAction extends Action
{


    protected function initAction()
    {

        $this->name = "刷新权限";
        $this->confirm('确定刷新权限吗？');
    }



    public function store()
    {
        try {

            $service = NodeService::instance();
            $service->updateNodes();
            $response = $this->response()->message('刷新成功');
        } catch (\Exception $exception) {
            $response = $this->response()->error($exception->getMessage());
        }
        return $response;
    }



}
