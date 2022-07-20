<?php

namespace plugins;


use quick\admin\QuickPluginService;

class Plugin extends QuickPluginService
{

    /**
     * init
     * @return $this
     */
    public function initPlugin()
    {
        $this->register();
        $this->boot();
        return $this;
    }


    /**
     * 注册资源事件。。。 已经安装的插件 系统每次request都会执行
     *
     * @return void
     */
    public function register()
    {


    }

    /**
     * 插件安装执行代码
     * @return mixed
     */
    public function install()
    {
        return true;
    }


    /**
     * 插件更新执行代码
     * @return mixed
     */
    public function update()
    {
        return true;
    }


    /**
     * 插件卸载执行代码
     * @return mixed
     */
    public function uninstall()
    {
        return true;
    }


    /**
     * 禁用插件
     * @return bool
     */
    public function disable()
    {
        return true;
    }


    /**
     * 启用插件
     * @return bool
     */
    public function enable()
    {
        return true;
    }


    /**
     * 插件安装之前
     */
    public function beforeInstall()
    {

    }


    /**
     * 插件安装之后
     */
    public function afterInstall()
    {

    }


    /**
     * 插件更新之前
     */
    public function beforeUpdate()
    {

    }


    /**
     * 插件更新之后
     */
    public function afterUpdate()
    {

    }


    /**
     * 插件卸载之前
     */
    public function beforeUninstall()
    {

    }


    /**
     * 插件卸载之后
     */
    public function afterUninstall()
    {

    }




}
