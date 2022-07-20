<?php

namespace app\admin\quick\actions;


use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\QueueService;
use think\Request;

/**
 * 重置任务
 * @AdminAuth(title="重置任务",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class ReQueueAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = 'quick\admin\http\model\SystemQueue';


    protected function initAction()
    {

        $this->name = "重置任务";
        $this->getDisplay()->type("text");
        $this->confirm('确定重置该任务吗？');
    }



    public function handle($model,Request $request)
    {

        try {

            $queue = QueueService::instance()->initialize($model['id'])->reset();
            $queue->progress(1, '>>> 任务重置成功 <<<', 0.00);
            $response = $this->response()->success('任务重置成功！',[]);
        } catch (\Exception $exception) {
            $response = $this->response()->error($exception->getMessage());
        }
        return $response;
    }


}
