<?php

namespace app\admin\quick\actions;


use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\PluginService;
use quick\admin\library\service\QueueService;
use think\Request;

/**
 * 卸载插件
 * @AdminAuth(title="卸载插件",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class PluginUninstallAction extends RowAction
{

    /**
     * 模型主键
     *
     * @var string
     */
    public static $pk = "name";

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = 'app\common\model\SystemPlugin';


    protected function initAction()
    {

        $this->name = "卸载";
        $this->getDisplay()->type('text')->style("color","red")->content( "卸载");
        $this->confirm('确定卸载吗?');

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

        return $this->handle($model, $this->request);
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

        return $this->resolve($this->request,$model);
    }


    public function handle($model,Request $request)
    {

        try {

//            if($model->status){
//                PluginService::instance()->disable($model->name);
//                $msg = '停用成功';
//                $model->status = 0;
//            }else{
//                PluginService::instance()->enable($model->name);
//                $model->status = 1;
//                $msg = '启用成功';
//
//            }
//            $model->save();
            PluginService::instance()->uninstall($this->getPkValue());
            $response = $this->response()->success('success')->message('卸载成功')
                ->event('admin_menu',[],200,true);

        } catch (\Exception $e) {
            $response = $this->response()->error($e->getMessage() ,[
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
        }
        return $response;
    }


}
