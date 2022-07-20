<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use components\admin\src\actions\RefreshNodeAction;
use Doctrine\Common\Annotations\AnnotationRegistry;
use quick\admin\library\service\NodeService;
use quick\admin\actions\BatchDeleteAction;
use quick\admin\library\tools\TreeArray;
use quick\admin\annotation\AdminAuth;
use app\common\model\SystemAuthNode;
use quick\admin\filter\Filter;
use quick\admin\table\Table;
use quick\admin\table\Query;
use quick\admin\Form\Form;
use quick\admin\Resource;
use quick\admin\table\TableTools;
use think\Request;

/**
 * 权限管理
 * @AdminAuth(title="权限管理",auth=true,login=true,menu=true)
 * @package app\admin\resource\auth
 */
class Auth extends Resource
{
    /**
     * @var string
     */
    protected $title = '权限管理';

    /**
     * @var string
     */
    protected $description = "系统权限管理";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemAuth";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["name"];


    /**
     * 过滤器
     * @param Request $request
     * @return array
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth(100);
        $filter->like("name", "name")->width(12);

        return $filter;
    }

    public function model(Query $model)
    {
        $model->where([
            ["plugin_name", "=", app()->http->getName()],
        ]);
        return $model;
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {

        $table->attribute('border', false);
        $table->column("id", "ID")->width(80)->sortable();
        $table->column("name", "权限名称");
        $table->column("desc", "权限描述");
        $table->column("create_time", "创建时间");
        $table->column("status", "状态")->switch(function () {
            $this->inactiveText("禁用")->activeText("启用")->width(55);
        })->width(100);

        $table->tools(function (TableTools $tools){
            $tools->add(new RefreshNodeAction());
        });
        return $table;
    }


    /**
     * @param Form $form
     * @param Request $request
     * @return bool|Form
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    protected function form(Form $form, Request $request)
    {

        $nodes = $this->_getNodes();
        $form->labelWidth(80);
        $form->text('name', "权限名称")->rules("require");
        $form->text('desc', "权限描述");
        $form->tree2('nodes', "权限")
            ->props([
                'valueKey' => 'node',
                'labelKey' => 'title',
            ])
            ->options($nodes)->fillUsing(function ($inputs, $model) {
            return $model->nodes = implode(',', $inputs['nodes']);
        })->resolveUsing(function ($value, $model) {
            if (isset($model['id'])) {
                $res = SystemAuthNode::where("auth", $model['id'])->column("node");
                return $res;
            }
        })->expandAll()->indent(28)->minWidth(90)->props("nodeKey", "node");


        return $form;
    }


    /**
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    private function _getNodes()
    {

        $nodes = NodeService::instance()->getNodes();
        $app_name = app()->http->getName();

        $nodes = collect($nodes)->filter(function ($node) use ($app_name) {
            if (!$node['is_auth']) {
                return false;
            }
            if ($app_name !== 'admin') {
                return $node['plugin_name'] === $app_name;
            }
            return true;
        })->toArray();

        return TreeArray::arr2tree($nodes, 'node', 'pnode', 'children');
    }


    /**
     * 注册行操作
     * @return array|mixed
     */
    protected function actions()
    {
        return [];
    }


    /**
     * 注册批量操作
     * @return array
     */
    protected function batchActions()
    {
        return [
            BatchDeleteAction::make("批量删除")
        ];
    }


    /**
     * @param $action
     * @param $request
     * @return mixed
     */
    protected function deleteAction($action, $request)
    {
        return $action->canRun(function ($request, $model) {
            return true;
        });
    }


    /**
     * @param \quick\actions\RowAction|\quick\admin\actions\Action $action
     * @param Request $request
     * @return \quick\actions\RowAction
     */
    protected function editAction($action, $request)
    {
        return $action->canRun(function ($request, $model) {

            return true;

        })->afterSaveUsing($this->_afterSaveFunc())->dialog();
    }


    /**
     * @param \quick\admin\actions\Action $action
     * @param Request $request
     * @return \quick\admin\actions\Action
     */
    protected function addAction($action, $request)
    {
        return $action->beforeSaveUsing(function ($data, $request) {
            $data['plugin_name'] = app()->http->getName();
            return $data;
        })->afterSaveUsing($this->_afterSaveFunc());
    }


    /**
     * @return \Closure
     */
    private function _afterSaveFunc()
    {
        return function ($data, $request) {

            if (empty($data['nodes']) && isset($data['id'])) {
                SystemAuthNode::where("auth", $data['id'])->delete();
                return true;
            }
            $nodes = explode(',', $data['nodes']);

            $saveData = [];
            foreach ($nodes as $node) {
                $saveData[] = [
                    'auth' => $data['id'],
                    'node' => $node,
                ];
            }
            isset($data['id']) && SystemAuthNode::where("auth", $data['id'])->delete();
            SystemAuthNode::insertAll($saveData);

        };
    }


}
