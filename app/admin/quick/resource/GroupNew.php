<?php

namespace app\admin\quick\resource;


use app\admin\quick\actions\GroupAddAction;
use app\admin\quick\actions\GroupDelAction;
use app\admin\quick\actions\GroupEditAction;
use app\common\model\SystemGroup;
use app\common\service\common\BuildGroupViewService;
use quick\admin\actions\Action;
use quick\admin\actions\BatchDeleteAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\components\layout\Row;
use quick\admin\filter\Filter;
use quick\admin\form\Form;
use quick\admin\Resource;
use quick\admin\table\Query;
use quick\admin\table\Table;
use think\Request;

/**
 *
 * @AdminAuth(title="配置组合",auth=true,login=true,menu=true)
 * @package app\admin\resource\auth
 */
class GroupNew extends Resource
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
    protected static $model = "app\\common\\model\\SystemGroupData";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["title"];

    protected $group_name;

    /**
     *
     */
    protected function init()
    {
        $group_name = request()->get('group_name');
        if (!$group_name) {
            $app_name = app()->http->getName();
            $where = [
                "plugin" => $app_name,
            ];
            $group = SystemGroup::where($where)->find();
            if ($group) {
                $group_name = $group->name;
            }
        }
        $this->group_name = $group_name;
    }


    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth("100");
        $filter->like("title", "title")->width(12);

        return false;
    }

    protected function model(Query $model)
    {

        $app_name = app()->http->getName();
        $where = [
            "plugin" => $app_name,
        ];
        if ($this->group_name) {
            $where["group"] = $this->group_name;
        }
        $model->where($where)->json(['value']);
        return $model->order("sort desc,id desc");
    }


    /**
     * @return Content
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function display()
    {


        $tabs = Component::tabs()->left()
            ->removeBottom()
            ->tabKey('group_name')
            ->props('isFilter', true);
        $groupList = SystemGroup::where(['plugin' => app()->http->getName() ])->select()->toArray();
        foreach ($groupList as $item) {
            $tabContent = Component::custom("el-row")->attribute([
                'type' => 'flex',
                'justify' => 'space-between',
                'align' => 'middle',
            ])->children([
                Component::custom('div')->children($item['title']),
                Component::custom('div')->children([
                    GroupEditAction::make('编辑')->param([
                        '_keyValues_' => $item['id']
                    ]),
                    GroupDelAction::make('删除')->param([
                        '_keyValues_' => $item['id']
                    ]),
                ])
            ]);
            $tabs->tab($tabContent, '', $item['name']);

        }
        $tabs->default((string)$this->group_name);


        $header = Component::custom('el-row')
            ->style([
                'justify-content' =>  'space-between',
                'align-items' =>  'center',
                'padding' =>  '0px 15px',
            ])
            ->children([
                Component::custom('span')->content('数据分组'),
                GroupAddAction::make('添加')->content('添加')
            ]);
        $tabs->children($header,'header');

        $content = Component::content();
        $content->title($this->title())
            ->description($this->description())
            ->body(function (Row $row) use ($tabs) {
                $row->props('gutter', 10);
                $row->col(["xs" => 5, "sm" => 24, "md" => 24, "lg" => 5], function ($col) use ($tabs) {
                    $col->children($tabs);
                });
                $row->col(["xs" => 24, "sm" => 24, "md" => 24, "lg" => 19,], $this->getTable()->displayRender());
            });

//
        return $content;
    }


    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {
        $groupInfo = SystemGroup::where('name', $this->group_name)->json(['fields'])->find();
        $table->attribute('border', false);
        $table->props('size', 'small');
        if(!empty($groupInfo)){
            $table->children(Component::card('div')->props('shadow','never')
                ->content("使用方法sysGroupData('{$groupInfo['name']}','{$groupInfo['plugin']}')"),'header');
        }

        $table->loadUrl($this->createUrl('index',[
            'group_name' => $this->group_name
        ]));
        $service = new BuildGroupViewService();
        $table = $service->getGroupDataTable($this->group_name,$table);

        return $table;
    }


    /**
     * @param Form $form
     * @param Request $request
     * @return bool|mixed|Form
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function form(Form $form, Request $request)
    {


        $groupInfo = SystemGroup::where([
            'name' =>  $this->group_name,
            'plugin' => app()->http->getName()
        ])->json(['fields'])->find();
        $list = [];
        if ($groupInfo) {
            $groupInfo = $groupInfo->toArray();
            foreach ($groupInfo['fields'] as $item) {
                $list[] = [
                    'type' => $item['type'],
                    'content' => $item['param'],
                    'name' => $item['name'],
                    'title' => $item['title'],
                    'rule' => $item['rule'] ?? '',
                    'value' => $item['value'] ?? '',
                    'desc' => $item['desc'] ?? '',
                ];
            }
        }


        $service = new BuildGroupViewService();
        $form = $service->getGroupDataForm($list,$form);
        $form->labelWidth(100);
        $form->extendData([
            'group_name' => $this->group_name,
        ]);

        $form->radio('status', '状态')->options([1 => '启用', 0 => '禁用'])->default(1);
        $form->inputNumber('sort', '排序')->default(1);
        $form->beforeResolveUsing(function ($model) {
            if ($model) {
                $data = $model->toArray();
                $value = json_decode($data['value'], true);
                return $data = array_merge($value, $data);
            }
            return $model;
        });
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
            if ($model['plugin'] != app()->http->getName()) {
                return false;
            }
            return true;
        });
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return \quick\actions\RowAction
     */
    protected function editAction(Action $action, $request)
    {
        $action = $action->param(['group_name' => $this->group_name]);
        return $action->canRun(function ($request, $model) {
            if ($model['plugin'] != app()->http->getName()) {
                return false;
            }
            return true;
        })->beforeSaveUsing(function ($data, $request) {

            $fields = $data;
            $data['plugin'] = app()->http->getName();
            unset($fields['id']);
            unset($fields['sort']);
            $data['value'] = json_encode($fields);
            return $data;
        })->dialog();
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return Action
     */
    protected function addAction(Action $action, $request)
    {
        $action = $action->param(['group_name' => $this->group_name]);
        return $action->beforeSaveUsing(function ($data, $request) {
            $data['plugin'] = app()->http->getName();
            $fields = $data;
            $data['value'] = json_encode($fields);
            $data['group'] = $request->post('group_name');
            return $data;
        })->dialog();
    }



}
