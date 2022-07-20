<?php
declare (strict_types = 1);

namespace quick\admin\actions;

use quick\admin\annotation\AdminAuth;
use think\Model;


/**
 * @AdminAuth(auth=true,menu=true,login=true,title="Delete")
 * @package quick\actions
 */
class DeleteAction extends RowAction
{
    public $name = "删除";

    protected function initAction()
    {

        $this->getDisplay()->style("color","red")->type('text');
        $this->confirm("你确定要删除吗？","提示");

    }


    public function handle(Model $model, $request)
    {

        $fields = $model->getFields();
        if(isset($fields['is_deleted'])){
            $model->is_deleted = 1;
            if(isset($fields['created_at'])){
                if(in_array($fields['created_at']['type'],['datetime','timestamp'])){
                    $model->created_at = date('Y-m-d H:i:s');
                }else{
                    $model->created_at = time();
                }
            }
            $res = $model->save();
        }else{
            $res = $model->delete();
        }


        if($res){
            return $this->response()->success()->message('删除成功');
        }
        return $this->response()->error("删除失败");
    }

}
