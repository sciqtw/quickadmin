<?php

namespace app\admin\quick\actions;


use quick\admin\actions\Action;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\ModuleService;
use quick\admin\library\service\QueueService;
use think\Request;

/**
 * 更新系统
 * @AdminAuth(title="更新系统",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class UpgradeQueueAction extends Action
{


    protected function initAction()
    {

        $this->name = "更新系统";
        $this->confirm('确定更新系统吗？');
    }


    public function store()
    {
        try {


            $data = ModuleService::instance()->installFile();
            $response = $this->response()->success('success',$data)->message('更新成功！');
        } catch (\Exception $exception) {
            $response = $this->response()->error($exception->getMessage(),[
                'file' => $exception->getFile(),
                'trace' => $exception->getTrace(),
                'line' => $exception->getLine(),
            ]);
        }
        return $response;
    }


}
