<?php

namespace app\admin\quick\actions;


use app\common\model\SystemAdmin;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\Exception;
use quick\admin\form\Form;
use quick\admin\library\service\QueueService;
use quick\admin\library\tools\CodeTools;
use think\Request;

/**
 * 删除数据
 * @AdminAuth(auth=true,menu=true,login=true,title="删除数据")
 * @package app\admin\resource\example\actions
 */
class GroupDelAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemGroup";


    protected function initAction()
    {
        $this->getDisplay()->type('text')->style("color","red")->size('small');
        $this->confirm('确定删除吗？');
    }



    public function handle($model,Request $request)
    {

        if($model->delete()){
            return $this->response()->success()->push($this->resource->createUrl('index'));
        }else{
            return $this->response()->error("删除失败");
        }
    }


}
