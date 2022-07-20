<?php

namespace app\admin\quick\resource;

use components\admin\src\actions\AdminEditAction;
use components\admin\src\actions\AdminPasswordAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\components\SettingItem;

/**
 *
 * @AdminAuth(title="个人中心",auth=true,menu=true,login=true)
 * @package app\admin\resource\auth
 */
class AdminSetting extends Resource
{
    /**
     * @var string
     */
    protected $title = '个人中心';

    /**
     * @var string
     */
    protected $description = "个人中心";


    /**
     * @AdminAuth(title="列表",auth=false,menu=true,login=true)
     * @param Content $content
     * @return \think\response\Json
     */
    public function index(Content $content)
    {

        return $this->success('success', $this->display());
    }


    /**
     * @return \quick\admin\components\layout\Content
     */
    protected function display()
    {

        $settingActions = new SettingItem();
        $settingActions->props('title','安全设置');
        $settingActions->addAction([
            new AdminPasswordAction(),
        ]);


        $tabs = Component::tabs()->setClass('quick-style-1')->default('-1')->left();
        $tabs->tab(Component::custom('div')
            ->style(['width' => '200px'])
            ->content('基本信息'), new AdminEditAction(), '-1');
        $tabs->tab(Component::custom('div')
            ->style(['width' => '200px'])
            ->content('安全设置'),$settingActions, '1');

        return Component::content()
            ->title($this->title())
            ->description($this->description())
            ->body($tabs);
    }

    /**
     * @AdminAuth(title="保存参数",auth=false,menu=true,login=true)
     * @return \think\response\Json
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {
        return $this->success('保存成功', []);
    }

}
