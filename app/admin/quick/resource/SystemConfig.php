<?php

namespace app\admin\quick\resource;


use app\common\model\SystemConfigGroup;
use app\common\service\common\BuildGroupViewService;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\form\Form;
use quick\admin\library\service\SystemService;


/**
 * 系统配置参数
 * @AdminAuth(auth=true,menu=true,login=true,title="系统参数")
 * Class SystemConfig
 * @package app\admin\quick\resource
 */
class SystemConfig extends Resource
{
    /**
     * 标题
     *
     * @var string
     */
    protected $title = '系统配置';

    /**
     * @var string
     */
    protected $description = "可以在此增改系统的变量和分组,也可以自定义分组和变量";

    /**
     * 关联模型 app\\common\\model\\DemoArticle
     *
     * @var string
     */
    protected static $model = 'app\common\model\SystemConfig';


    /**
     * 可搜索字段
     * @var array
     */
    public static $search = ["id"];


    /**
     *
     * @return Content
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function display()
    {
        $tabKey = 'group';
        $groupList = SystemConfigGroup::where([
            'status' => 1,
            'parent_id' => 0,
        ])->order('sort desc,id asc')->select();

        $defaultGroupId = $this->request->param($tabKey,0);

        $topTabs = Component::tabs()
            ->top()
            ->isFilter()
            ->tabKey($tabKey)
            ->removeBottom();

        $currentTopGroup = null;
        foreach ($groupList as $group) {
            if(empty($currentTopGroup) && ($group->id == $defaultGroupId || empty($defaultGroupId)) ){
                $currentTopGroup = $group;
                $defaultGroupId = $group->id;
            }
            $topTabs->tab($group->title,'',$group->id);
        }
        $topTabs->default($defaultGroupId);



        $groupList = SystemConfigGroup::where([
            'status' => 1,
            'parent_id' => $defaultGroupId,
        ])->order('sort desc,id asc')->select();

        $configList = \app\common\model\SystemConfig::getConfigList();

        $createForm = function ($fields,$group){
            $service = new BuildGroupViewService();
            $form = $service->getGroupDataForm($fields);
            $form->url($this->createUrl("list?_group={$group}"));
            $form->hideCancel();
            return $form;
        };

        if(empty($groupList->toArray()) && $currentTopGroup){
            $fields = $configList[$currentTopGroup->group] ?? [];
            $content = $createForm($fields, $currentTopGroup->group);
        }else{
            $tabs = Component::tabs()->borderCard();
            foreach ($groupList as  $tab) {
                $fields = $configList[$tab->group] ?? [];
                $form = $createForm($fields, $tab->group);
                $tabs->tab($tab->title, $form);
            }
            $tabs->default('0');
            $content = $tabs;
        }



        return Component::content()
            ->title($this->title())
            ->description($this->description())
            ->children($topTabs, 'tools')
            ->body($content);
    }



    /**
     * @AdminAuth(title="保存参数",auth=true,menu=true,login=true)
     * @return \think\response\Json
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        $request = request();
        $group = request()->get('_group');
        $config = SystemService::instance();
        if (!empty($group)) {
            $configList = \app\common\model\SystemConfig::getConfigList();
            $fields = $configList[$group] ?? [];
            if (!empty($fields)) {
                $service = new BuildGroupViewService();
                $form = $service->getGroupDataForm($fields);
//                $form->fixedFooter();
                $data = $form->getSubmitData($request, 3);
                foreach ($fields as $field) {
                    if (isset($data[$field['name']])) {
                        $config->set($field['group'] . "." . $field['name'], $data[$field['name']], 'admin',$field['type']);
                    }
                }
                return $this->success('保存成功', $fields);
            }

        }
        return $this->error('error');
    }




}
