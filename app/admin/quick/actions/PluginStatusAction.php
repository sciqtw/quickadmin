<?php

namespace app\admin\quick\actions;


use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\library\service\PluginService;
use quick\admin\library\service\QueueService;
use think\Request;

/**
 * 插件状态
 * @AdminAuth(title="插件状态",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class PluginStatusAction extends RowAction
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

        if(empty($this->data['status'])){
            $this->name = "开启";
            $this->display(Component::button($this->name())->type('text') );
            $this->confirm('确定开启');
        }else{
            $this->name = "禁用";
            $this->display(Component::button($this->name())->type('text')->style("color","red")->type('text'));
            $this->confirm('确定禁用');
        }

    }



    public function handle($model,Request $request)
    {

        try {

            if($model->status){
                PluginService::instance()->disable($model->name);
                $msg = '停用成功';
                $model->status = 0;
            }else{
                PluginService::instance()->enable($model->name);
                $model->status = 1;
                $msg = '启用成功';

            }
            $model->save();
            $response = $this->response()->success('success')->message($msg)
                ->event('admin_menu',[],200,true);

        } catch (\Exception $exception) {
            $response = $this->response()->error($exception->getMessage());
        }
        return $response;
    }


}
