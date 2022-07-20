<?php

namespace app\admin;

use components\admin\src\AdminAuth;
use components\admin\src\attachment\Attachment;
use components\vcharts\src\VchartsTool;
use quick\admin\Quick;
use quick\admin\QuickPluginService;
use quick\Sys;


class Plugin extends QuickPluginService
{

    /**
     * 名称
     *
     * @var string
     */
    public $name = "admin";


    /**
     * 应用唯一标识
     *
     * @var string
     */
    public $app_key = "admin";


    /**
     * 第三方工具
     * @return array
     */
    public function tools():array
    {
        return [
            new AdminAuth(),
            new VchartsTool()
        ];
    }


    /**
     * @return array
     */
    public function script():array
    {

        return [];
    }


    /**
     * @return array
     */
    public function style():array
    {
        return [];
    }


    /**
     * @return array
     */
    public function resources(): array
    {
        return [
             Attachment::class
        ];
    }


    /**
     * 本地组件路径
     * @return string
     */
    public function viewComponentPath()
    {
        return 'components/component';
    }



    /**
     * 渲染组件
     * @return bool
     */
    public function readerComponent()
    {


        $config = [
            'view_path'	=>	root_path("app/admin/view"),
            'cache_path'	=>	root_path('/runtime/template'),
            'view_suffix'   =>	'html',
        ];
        $template = new \think\Template($config);
        $template->fetch($this->viewComponentPath());
    }


    /**
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function boot()
    {

        $plugins = Sys::instance()->plugin->getPlugins();
        $list = [];
        foreach ($plugins as $plugin){
            $list[] = $plugin['name'];
        }
        // loadPlugins
        Sys::instance()->plugin->registerPlugins($list);
        parent::boot();
    }


}
