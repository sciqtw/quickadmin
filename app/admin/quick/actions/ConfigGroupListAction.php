<?php

namespace app\admin\quick\actions;


use quick\admin\actions\Action;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\http\response\JsonResponse;
use quick\admin\library\tools\TreeArray;

/**
 * 设置密码
 * Class ConfigGroupListAction
 * @AdminAuth(title="配置分组列表",auth=true,login=true,menu=false)
 * @package app\admin\quick\actions
 */
class ConfigGroupListAction extends Action
{



    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemConfigGroup";


    protected function init()
    {


        $tree = Component::tree([],'配置分组')->border()
            ->lazy($this->loadUrl())
            ->showExpandAll()
                ->props('default',$this->request->param('tab_key'))
            ->height(500)
            ->defaultAllOpen()
            ->isFilter()
            ->addAction( ConfigGroupAddAction::make('添加'),3)
            ->addAction(  ConfigGroupEditAction::make('编辑'))
            ->addAction(  ConfigGroupDelAction::make('删除'))
            ->headerAction( ConfigGroupAddAction::make('添加'));
        $this->display($tree);

        return parent::init(); // TODO: Change the autogenerated stub
    }

    protected function initAction()
    {

//        $this->dialog();
    }


    /**
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function load()
    {

        if (!$this->handleCanRun($this->request, [])) {
            quick_abort(500, '你无权访问');
        }
        $list = $this->getModel()->field('id,parent_id,title as label')->order('sort desc,id asc')->select()->toArray();
        $treeData = TreeArray::arr2tree($list,'id','parent_id','children');
        $treeData = [
            [
                'id' => -1,
                'label' => '全部',
                'children' => $treeData
            ]
        ];
        return $this->response()->success("success",$treeData);
    }


}
