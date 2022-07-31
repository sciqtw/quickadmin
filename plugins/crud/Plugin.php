<?php
declare (strict_types = 1);

namespace plugins\crud;


use quick\admin\Quick;
use think\facade\Template;

class Plugin extends \plugins\Plugin
{

    public function component()
    {

    }

    /**
     * 名称
     *
     * @var string
     */
    public $name = "crud";


    /**
     * @var string
     */
    public $app_key = "crud";

    /**
     * 第三方工具
     * @return array
     */
    public function tools():array
    {
        return [
//            new AdminAuth()
        ];
    }



    /**
     * @return array
     */
    public function script():array
    {

        return [
//            'mall' => __DIR__.'/tools/dist/js/index.js',
            'crud' => __DIR__.'/tools/dist/js/field.js',
//            'chunk-vendors' => __DIR__.'/tools/dist/js/chunk-vendors.js',
        ];
    }


    /**
     * @return array
     */
    public function style():array
    {
        return [];
    }


    /**
     * 注册资源
     * @return array
     */
    public function resources():array
    {
        return [];
    }


}
