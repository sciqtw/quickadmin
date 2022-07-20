<?php

namespace app\admin\quick\actions;


use quick\admin\actions\Action;
use quick\admin\annotation\AdminAuth;
use quick\admin\http\response\JsonResponse;

/**
 * Class ConfigGroupDelAction
 * @AdminAuth(title="删除配置分组",auth=true,login=true,menu=false)
 * @package app\admin\quick\actions
 */
class ConfigGroupDelAction extends Action
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemConfigGroup";


    protected function initAction()
    {
        $this->getDisplay()->type('text');

        $this->confirm('删除分组','提示');
    }



    /**
     * @return ConfigGroupEditAction
     */
    public function isEdit()
    {
        return $this->param(['type' => 'edit']);
    }



    /**
     * 动作提交数据接口
     *
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function store()
    {

        $model = $this->getModel()->where([
            'id' => $this->request->post('id/d')
        ])->find();

        if (!$model) {
            return $this->response()->error('数据有误');
        }

        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        try {

            $res = $model->delete();
            if (!$res) {
                throw new \Exception("删除失败");
            }
            return $this->response()->success("删除成功")->event('refresh', [], 0, true);

        } catch (\Exception $e) {
            return $this->response()->error($e->getMessage());
        }

    }


}
