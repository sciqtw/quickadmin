<?php

namespace app\admin\quick\resource;

use app\admin\quick\actions\PluginInstallAction;
use app\admin\quick\actions\PluginStatusAction;
use app\admin\quick\actions\PluginUninstallAction;
use app\admin\quick\actions\PluginUserInfoAction;
use quick\admin\actions\BatchDeleteAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\filter\Filter;
use quick\admin\Form\Form;
use quick\admin\Resource;
use quick\admin\table\Table;
use think\Request;

/**
 * Class Plugin
 * @AdminAuth(title="插件管理",auth=true,menu=true,login=true)
 * @package app\admin\quick\resource
 */
class Plugin extends Resource
{
    /**
     * @var string
     */
    protected $title = '插件管理';

    /**
     * @var string
     */
    protected $description = "系统插件管理";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemOnlinePlugin";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["name"];


    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth("100");
        $filter->equal("version", "类型")->width(8)->radio([0 => '全部', 1 => '收费', 2 => '免费', 3 => '本地']);
        $filter->equal("name", "名称")->width(7);

        return false;
    }

    protected function display()
    {
        $tabKey = 'type';
        $topTabs = Component::tabs()
            ->top()
            ->isFilter()
            ->tabKey($tabKey)
            ->default('-1')
            ->removeBottom();
        $topTabs->tab('全部', '', '-1');
//        $topTabs->tab('完整应用','','1');
//        $topTabs->tab('功能插件','','2');
//        $topTabs->tab('前端组件','','3');
//
        $description = Component::row('')
            ->col('12','可在线安装、卸载、禁用、启用、配置、升级插件，插件升级前请做好备份。')
            ->col('12',PluginUserInfoAction::make());

        return Component::content()
            ->title($this->title())
            ->children( $description ,'description')
            ->children($topTabs, 'tools')
            ->body($this->getTable()->displayRender());
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {

//        $table->disableFilter();
//        $table->column("id", "ID")->width(80)->sortable();
//        $table->column("index", "前端")->width(60);
        $table->column("display_name", "名称");
        $table->column("desc", "介绍");
        $table->column("avatar", "logo")->image(40,40)->width(100);
        $table->column("author", "作者")->width(100)->default('Quick');
        $table->column("price", "价格")->display(function ($value, $row) {
//            if (!empty($row['is_install'])) {
//                $this->label("success", '', 'plain');
//                return '已安装';
//            } else
            if ($value > 0) {
                $this->label("danger", '', 'plain');
            } else {
                $this->label("success", '', 'plain');
            }
            return $value > 0 ? '￥' . $value : '免费';
        })->width(100);
        $table->column("download_num", "下载")->default(0)->width(100);
        $table->column("addonVersion", "版本号")->default('1.0.0')->display(function ($value,$model){
            if(is_array($value) && !empty($value)){
                return  $value[0]['version'];
            }
            return $model['version'];
        })->width(100);

        $table->actions(function ($action) {
            $action->add(PluginStatusAction::make()->canRun(function ($request, $model) {
                if (empty($model['is_install'])) {
                    return false;
                }
                return true;
            }));
            $action->add(PluginInstallAction::make()->canRun(function ($request, $model) {
                if (empty($model['is_install'])) {
                    return true;
                }
                return false;
            }));
            $action->add(PluginUninstallAction::make()->canRun(function ($request, $model) {
                if (!empty($model['is_install']) && !$model['status']) {
                    return true;
                }
                return false;
            }));
        });

        return $table;
    }


    /**
     * @param Form $form
     * @param Request $request
     * @return Form
     */
    protected function form(Form $form, Request $request)
    {

        $form->labelWidth(80)->size('mini');
        $form->text('name', "插件")->rules('require')
            ->creationRules('unique:SystemPlugin')
            ->resolveUsing(function ($value, $row) {
                if (isset($row['id'])) {
                    $this->disabled();
                }
                return $value;
            });
        $form->text('display_name', "名称")->rules('require');
        $form->text('desc', "介绍");


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
//            BatchDeleteAction::make("批量删除")
        ];
    }


}
