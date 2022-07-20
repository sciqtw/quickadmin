<?php

namespace app\admin\quick\actions;


use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\library\service\QueueService;

/**
 * 任务状态
 * @AdminAuth(title="任务状态",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class QueueLogAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = 'quick\admin\http\model\SystemQueue';



    public function resolve($request, $model)
    {
        $data = QueueService::instance()->initialize($model->id)->progress();
        if($request->param('type') == 4){
            return $this->response()->success('success',$data );
        }

        $res = Component::custom('queue-log')->props('url',$this->createUrl('load?type=4&_keyValues_='.$model->id));
        return $this->response()->success('success',$res );
    }




}
