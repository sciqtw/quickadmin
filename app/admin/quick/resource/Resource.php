<?php

namespace app\admin\quick\resource;

use quick\admin\actions\Action;
use think\Request;

abstract class Resource extends \quick\admin\Resource
{

    /**
     * 标题
     *
     * @var string
     */
    protected $title = 'name';

    /**
     * @var string
     */
    protected $description = "description";

    /**
     * 基础模型资源实例.
     *
     * @var \think\Model
     */
    public $resource;


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
        return [];
    }





}
