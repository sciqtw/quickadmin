<?php
declare(strict_types=1);

namespace components\admin\src;


use app\Request;

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


    public function __construct(Request $request)
    {

        parent::__construct($request);
    }




}
