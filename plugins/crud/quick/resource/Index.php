<?php

namespace plugins\crud\quick\resource;


use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\Resource;
use think\Exception;

/**
 *  crud
 * @AdminAuth(title="首页",auth=true,login=true,menu=true)
 * @package app\admin\resource\example
 */
class Index extends Resource
{
    /**
     * 标题
     *
     * @var string
     */
    protected $title = 'CRUD';

    /**
     * @var string
     */
    protected $description = "crud工作台";



    /**
     * @AdminAuth(title="数据列表")
     * @param Content $content
     * @return \think\response\Json
     * @throws Exception
     */
    public function index(Content $content)
    {

        $content->title($this->title())
            ->description($this->description())
            ->body(Component::custom('qk-crud'));

        return $this->success('success', $content);
    }


    /**
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
