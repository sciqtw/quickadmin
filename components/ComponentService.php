<?php
declare (strict_types = 1);

namespace components;


use quick\admin\Quick;

class ComponentService  extends \think\Service
{

    /**
     * 注册服务
     *
     * @return mixed
     */
    public function register()
    {

        Quick::registerService([
//            UploadServiceProvider::class
        ]);

    }


    /**
     * 执行服务
     *
     * @return mixed
     */
    public function boot()
    {

        $components = Quick::services();
        foreach ($components as $component){
            (new $component)->boot();
        }

    }


}
