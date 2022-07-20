<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use quick\admin\actions\AddAction;
use quick\admin\actions\BatchDeleteAction;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\filter\Filter;
use quick\admin\Form\Form;
use quick\admin\library\service\MenuService;
use quick\admin\library\service\NodeService;
use quick\admin\library\tools\TreeArray;
use quick\admin\table\action\ActionColumn;
use quick\admin\table\Query;
use quick\admin\Resource;
use quick\admin\table\Table;
use think\Request;

/**
 * 菜单管理
 * @AdminAuth(title="菜单管理",auth=true,menu=true,login=true)
 * Class Menus
 * @package app\admin\resource\auth
 */
class Menus extends Resource
{
    /**
     * @var string
     */
    protected $title = '菜单管理';

    /**
     * @var string
     */
    protected $description = "系统菜单管理--商家不需要菜单管理，后面删除";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemMenu";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["title"];


    public function model(Query $model)
    {
        $model->where([
            ["plugin_name", "=", app()->http->getName()],
        ]);
        return $model->order("sort desc,id desc");
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {

        $table->attribute('border', false);
        $table->disablePagination();
        $table->disableFilter();

        $table->dataUsing(function ($data) {
            $data = array_merge($data->toArray());
            return TreeArray::arr2tree($data, "id", "pid", "children");
        });
        $table->tree(false);
        $table->column("title", "菜单")->display(function ($value, $row, $v) {
            return  $row['title'];
        })->width(350);

        $table->column("id", "ID")->display(function ($value, $row, $v) {
            return  $value;
        })->width(100)->hide();

        $table->column("icon", "图标")->icon(20, "#67c23a")->width(70);

        $table->column("path", "链接")->width(300)->label("success", '', 'plain');
        $table->column("badge", "标记")->width(60);
        $table->column("sort", "排序")->input();
        $table->column("status", "启用状态")->switch(function () {
            $this->inactiveText("禁用")
                ->activeText("启用")->width(55);
        })->width(90);
        $table->actions(function (\quick\admin\table\ActionColumn $action){
            $action->add((new AddAction('添加'))->display(function($display){
                $display->type("text");
            }));
        });

        return $table;
    }


    /**
     * 过滤器
     * @param Request $request
     * @return array
     */
    protected function filters(Filter $filter)
    {

//        $filter->labelWidth("100");
//        $filter->equal("username", "username")->width(12);

        return false;
    }

    /**
     * @param Form $form
     * @param Request $request
     * @return bool|Form
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function form(Form $form, Request $request)
    {

        $nodes = NodeService::instance()->getMenuNodes();


        $form->labelWidth(80)->size('mini');
        $menus = self::newModel()->where([
            "status" => 1,
            "plugin_name" => app()->http->getName(),
        ])->select()->toArray();
        array_unshift($menus, ['id' => 0, 'title' => "顶级菜单", 'pid' => "-1"]);
        $menus = TreeArray::arr2table($menus, "id", "pid", "url_key");
        foreach ($menus as &$menu) {
            $menu['title'] = $menu['spl'] . $menu['title'];
        }


        $form->select("pid", "上级菜单")->options($menus, 'id', 'title')->resolveUsing(function ($value,$model){
            $defaultValue = request()->param(AddAction::$keyName,0);
            if(empty($model['id'])){
                return $value ? $value:$defaultValue;
            }
            return $value;
        })->help('必选，请选择上级菜单或顶级菜单（目前最多支持三级菜单）');
        $form->text('title', "菜单标题")->rules("require")->help('必选，请填写菜单名称（如：系统管理），建议字符不要太长，一般4-6个汉字');
        $form->text('path', "菜单链接")->autocomplete($nodes)
            ->children(Component::custom('input-item'),'default')
            ->rules("require")->help('必选，请填写链接地址或选择系统节点（如：https://domain.com/admin/user/index.html 或 admin/user/index）
当填写链接地址时，以下面的“权限节点”来判断菜单自动隐藏或显示，注意未填写“权限节点”时将不会隐藏该菜单哦');
        $form->text('params', "链接参数")->help('可选，设置菜单链接的GET访问参数（如：name=1&age=3）');
        $form->text('node', "权限节点")->autocomplete($nodes)->help('可选，请填写系统权限节点（如：admin/user/index），未填写时默认解释"菜单链接"判断是否拥有访问权限；');
        $form->icon('icon', "菜单图标")->help('可选，设置菜单选项前置图标');

        $form->row(function ($row) {
            $row->col(12, function ($form) {
                $form->text('badge', "标记")->help('可选，设置菜单选项前置图标');

            });
            $row->col(12, function ($form) {
                $form->inputNumber('sort', "排序")->help('可选，设置菜单选项前置图标');
            });
        });


        return $form;
    }


    /**
     * 注册行操作
     * @return array|mixed
     */
    protected function actions()
    {
        return [

        ];
    }


    /**
     * 注册批量操作
     * @return array
     */
    protected function batchActions()
    {
        return [
//            BatchDeleteAction::make("批量删除")
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
     * @param RowAction $action
     * @param Request $request
     * @return \quick\actions\RowAction|RowAction
     */
    protected function editAction($action, $request)
    {
        return $action->canRun(function ($request, $model) {
            return true;
        })->dialog();
    }


    /**
     * @param \quick\actions\Action $action
     * @param Request $request
     * @return \quick\actions\Action
     */
    protected function addAction($action, $request)
    {
        return $action->beforeSaveUsing(function ($data, $request) {
            $data['plugin_name'] = app()->http->getName();
            return $data;
        });
    }


}
