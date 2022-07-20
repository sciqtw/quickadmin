<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use components\admin\src\actions\GroupDataAction;
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
 * 配置组合
 * @AdminAuth(title="配置组合",auth=true,login=true,menu=true)
 * @package app\admin\resource\auth
 */
class Group extends Resource
{
    /**
     * @var string
     */
    protected $title = '组合配置';

    /**
     * @var string
     */
    protected $description = "系统参数组合配置";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemGroup";


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
            $model->where("plugin",$app_name);
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
        $table->column("title", "数据组名称");
        $table->column("info", "简介");
        $table->column("name", "key");


        return $table;
    }



    /**
     * @param Form $form
     * @param Request $request
     * @return Form
     */
    protected function form(Form $form, Request $request)
    {

//        $form->labelWidth(100);
        $form->text('title', "数据组名称")->rules('require');
        $form->text('info', "数据简介")->rules('require');
        $form->text('name', "数据字段")->rules('require')
            ->creationRules('unique:SystemGroup');


        $form->dynamic('fields', '自定义字段')->form(function (Form $form) {


            $style = [
                'flex' => '1',
                'width' => '50%',
                'margin-right' => '10px',
            ];
            $options = [
                'text' => '文本框',
                'textarea' => '多行本框',
                'select' => '下拉选择',
                'radio' => '单选',
                'checkbox' => '多选',
                'file' => '文件',
                'image' => '单图',
                'images' => '多图',
            ];
            $form->text('title', '字段名称')->style($style)->placeholder('字段title')->required()->hiddenLabel();
            $form->text('name', '字段name')->style($style)->placeholder('字段name')->required()->hiddenLabel();
            $form->text('rule', '规则')->style($style)->placeholder('字段rule')->required()->hiddenLabel();
            $form->select('type', '字段类型')->options($options)->placeholder('字段类型')
                ->required()
                ->hiddenLabel()
                ->when('in', ['select', 'radio', 'checkbox'], function (Form $form) {
                    $form->text('param', '字段配置')->textarea()
                        ->style([
                            'width' => '100%',
                        ])
                        ->placeholder("参数方式例如:\n1=白色\n2=红色\n3=黑色")
                        ->hiddenLabel();
                });
            return $form;

        })->fillUsing(function ($data) {
            return json_encode($data['fields']);
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
            new GroupDataAction('数据列表')
        ];
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
            if( $model['plugin'] != app()->http->getName()){
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
            if($model['plugin'] != app()->http->getName()){
                return false;
            }
            return true;
        });
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return Action
     */
    protected function addAction(Action $action, $request)
    {
        return $action->beforeSaveUsing(function ($data, $request) {
            $data['plugin'] = app()->http->getName();
            return $data;
        });
    }



}
