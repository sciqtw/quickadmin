<?php

namespace app\admin\quick\actions;



namespace quick\admin\actions;

use quick\admin\annotation\AdminAuth;

/**
 * 批量删除
 * @AdminAuth(auth=true,menu=true,login=true,title="Batch delete action")
 * @package app\admin\resource\example\actions
 */
class BatchDeleteAction extends BatchAction
{


    /**
     * @return BatchAction|void
     */
    protected function initAction()
    {
        $this->getDisplay()->type("danger");
        $this->confirm("确定批量删除！", '提示');
    }


    public function handle($models, $request)
    {

        foreach ($models as $model) {
            $fields = $model->getFields();
            if(isset($fields['is_deleted'])){
                $model->is_deleted = 1;
                $res = $model->save();
            }else{
                $res = $model->delete();

            }
            if(!$res){
                return $this->response()->error('')->message('删除失败');
            }

        }
        return $this->response()->success('')->message('删除成功');
    }


}
