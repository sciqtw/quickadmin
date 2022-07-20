<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use quick\admin\actions\Action;
use quick\admin\actions\BatchDeleteAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\filter\Filter;
use quick\admin\Form\Form;
use quick\admin\form\layout\Row;
use quick\admin\Resource;
use quick\admin\table\Query;
use quick\admin\table\Table;
use think\Request;

/**
 * 组合数据
 * @AdminAuth(title="组合数据",auth=true,login=true,menu=true)
 * @package app\admin\resource\auth
 */
class GroupData extends Resource
{
    /**
     * @var string
     */
    protected $title = '组合数据';

    /**
     * @var string
     */
    protected $description = "系统参数组合配置";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemGroupData";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["title"];


    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth(100);
        $filter->like("title", "title")->width(12);

        return $filter;
    }

    public function model(Query $model)
    {

        $app_name = app()->http->getName();
        if($app_name !== 'admin'){
            $model->where("plugin_name",$app_name);
        }
        return $model->order("id desc");
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
        $table->column("name", "key");
        $table->column("title", "数据组名称");
        $table->column("info", "简介");
//        $table->disableActions();


        return $table;
    }



    /**
     * @param Form $form
     * @param Request $request
     * @return Form
     */
    protected function form(Form $form, Request $request)
    {

        $form->labelWidth(100);
        $form->text('title', "数据组名称")->rules('require');
        $form->text('info', "数据简介")->rules('require');
        $form->text('name', "数据字段")->rules('require')
            ->creationRules('unique:SystemGroup');
//        $form->text('display_name', "名称")->rules('require');
        $form->dynamic('fields','添加字段')->form(function (Form $form){
            $form->row(function (Row $row) {
                $row->props('gutter', 10);
                $row->col(12, function (Form $form) {
                    $form->select('type','字段类型')->options([
                        'text' => '文本框',
                        'textarea' => '多行本框',
                        'select' => '下拉选择',
                        'radio' => '单选',
                        'checkbox' => '多选',
                        'file' => '文件',
                        'image' => '单图',
                        'images' => '多图',
                    ])->placeholder('字段类型')
                        ->required()
                        ->hiddenLabel()
                        ->when('in',['select','radio','checkbox'],function (Form $form){
                            $form->text('param','字段配置')->textarea()
                                ->placeholder("参数方式例如:\n1=白色\n2=红色\n3=黑色")
                                ->hiddenLabel();
                        });

                });
                $row->col(6, function (Form $form) {
                    $form->text('title','字段名称')->placeholder('字段名称')->required()->hiddenLabel();
                });
                $row->col(6, function (Form $form) {
                    $form->text('name','字段name')->placeholder('字段name')->required()->hiddenLabel();

                });

            });
            return $form;

        })->fillUsing(function ($data){
            return json_encode($data['fields']);
        });
//        $form->table('json_text', 'table',function ($table){
//            $table->column('key', 'key')->field(function (Form $form) {
//                return $form->text('key')->required()->when('n',function (Form $form){
//                    $form->text('dfd','d');
//                });
//            });
//            $table->column('value', 'value')->field(function ($form) {
//                return $form->text('value')->required();
//            });
//            $table->column('desc', 'desc')->field(function ($form) {
//                return $form->text('desc')->required();
//            });
//        })->min(3);

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
        return $action->canRun(function ($request, $model) {
            if( $model['plugin_name'] != app()->http->getName()){
                return false;
            }
            return true;
        });
    }

    /**
     * @param Action $action
     * @param Request $request
     * @return \quick\actions\RowAction|Action
     */
    protected function editAction(Action $action, $request)
    {
        return $action->canRun(function ($request, $model) {
            if($model['plugin_name'] != app()->http->getName()){
                return false;
            }
            return true;
        })->page();
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return Action
     */
    protected function addAction(Action $action, $request)
    {
        return $action->beforeSaveUsing(function ($data, $request) {
            $data['plugin_name'] = app()->http->getName();
            return $data;
        })->page();
    }



}
