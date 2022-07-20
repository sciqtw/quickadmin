<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use quick\admin\actions\BatchDeleteAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\filter\Filter;
use quick\admin\Form\Form;
use quick\admin\Resource;
use quick\admin\table\Query;
use quick\admin\table\Table;
use think\Request;

/**
 * 节点管理
 * @AdminAuth(title="节点管理",auth=true,menu=true,login=true)
 * Class Node
 * @package app\admin\resource\auth
 */
class Node extends Resource
{
    /**
     * @var string
     */
    protected $title = '菜单节点';

    /**
     * @var string
     */
    protected $description = "系统节点管理";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemNode";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["node"];


    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth(100);
        $filter->equal("username", "username")->width(12);

        return $filter;
    }

    public function model(Query $model)
    {

        $app_name = app()->http->getName();
        if($app_name !== 'admin'){
            $model->where("plugin_name",$app_name);
        }
        return $model->order("sort desc,id desc");
    }


    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {

        $table->disableFilter();
        $table->disablePagination();
        $table->attribute('border', false);
        $table->column("id", "ID")->width(80)->sortable();
        $table->column("node", "节点");
        $table->column("title", "名称");
        $table->disableActions();


        return $table;
    }



    /**
     * @param Form $form
     * @param Request $request
     * @return Form
     */
    protected function form(Form $form, Request $request)
    {
        return $form;
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
        return false;
    }

    /**
     * @param \quick\actions\Action|\quick\actions\RowAction $action
     * @param Request $request
     * @return \quick\actions\Action|\quick\actions\RowAction
     */
    protected function editAction($action, $request)
    {
        return false;
    }


    /**
     * @param \quick\actions\Action $action
     * @param Request $request
     * @return \quick\actions\Action
     */
    protected function addAction($action, $request)
    {
        return false;
    }


}
