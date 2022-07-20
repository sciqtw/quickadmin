<?php
declare (strict_types = 1);

namespace quick\admin;

use think\App;
use think\Container;

/**
 * 自定义服务基类
 * Class Service
 * @package think\admin
 */
abstract class Service
{
    /**
     * 应用实例
     * @var App
     */
    protected $app;


    /**
     * Service constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->initialize();
    }


    /**
     * 初始化服务
     *
     * @return $this
     */
    protected function initialize(): Service
    {
        return $this;
    }


    /**
     * 静态实例对象
     *
     * @param array $var
     * @param bool $new
     * @return static
     */
    public static function instance(array $var = [], bool $new = false)
    {
        return Container::getInstance()->make(static::class, $var, $new);
    }
}