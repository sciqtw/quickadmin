<?php


namespace quick;


use components\admin\src\auth\Plugin;
use quick\admin\library\service\AuthService;
use quick\admin\library\service\PluginService;
use quick\admin\Service;
use think\Cache;
use think\Config;
use think\Console;
use think\Cookie;
use think\Db;
use think\Env;
use think\Event;
use think\Filesystem;
use think\Http;
use think\Lang;
use think\Log;
use think\Middleware;
use think\Request;
use think\Route;
use think\Session;
use think\Validate;


/**
 * Class Sys
 * @property Route      $route
 * @property Config     $config
 * @property Cache      $cache
 * @property Request    $request
 * @property Http       $http
 * @property Console    $console
 * @property Env        $env
 * @property Event      $event
 * @property Middleware $middleware
 * @property Log        $log
 * @property Lang       $lang
 * @property Db         $db
 * @property Cookie     $cookie
 * @property Session    $session
 * @property Validate   $validate
 * @property Filesystem $filesystem
 * @property PluginService $plugin
 * @property AuthService $auth
 * @package quick
 */
class Sys  extends Service
{


    public function initialize(): Service
    {
        $this->app->bind('plugin',PluginService::class);
        $this->app->bind('auth',AuthService::class);
        return parent::initialize();
    }


    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->app->bind($name, $value);
    }


    /**
     * @param $name
     * @return object
     */
    public function __get($name)
    {
        return $this->app->get($name);
    }


}