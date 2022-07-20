<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace quick\admin\multiple;

use Closure;
use think\App;
use think\exception\HttpException;
use think\Request;
use think\Response;

/**
 * 多应用模式支持
 */
class MultiApp
{

    /** @var App */
    protected $app;

    /**
     * 应用名称
     * @var string
     */
    protected $name;

    /**
     * 应用名称
     * @var string
     */
    protected $appName;

    /**
     * 应用路径
     * @var string
     */
    protected $path;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->name = $this->app->http->getName();
        $this->path = $this->app->http->getPath();
    }

    /**
     * 多应用解析
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if (!$this->parseMultiApp()) {
            return $next($request);
        }

        return $this->app->middleware->pipeline('app')
            ->send($request)
            ->then(function ($request) use ($next) {
                return $next($request);
            });
    }

    /**
     * 获取路由目录
     * @access protected
     * @return string
     */
    protected function getRoutePath(): string
    {
        return $this->app->getAppPath() . 'route' . DIRECTORY_SEPARATOR;
    }

    /**
     * 解析多应用
     * @return bool
     */
    protected function parseMultiApp(): bool
    {
        $has_run_key = defined('Quick_APP_RUN_KEY');
        $scriptName = $this->getScriptName();
        $defaultApp = $this->app->config->get('app.default_app') ?: 'index';

        $quick_app_run_key = $this->app->config->get('app.quick_app_run_key', '');
        if($has_run_key && $quick_app_run_key == $scriptName){
            $scriptName = 'index';
        }

        if ($this->name || ($scriptName && !in_array($scriptName, ['index', 'router', 'think']))) {
            $appName = $this->name ?: $scriptName;
            $this->app->http->setBind();
        } else {
            // 自动多应用识别
            $this->app->http->setBind(false);
            $appName = null;
            $this->appName = '';

            $bind = $this->app->config->get('app.domain_bind', []);

            if (!empty($bind)) {
                // 获取当前子域名
                $subDomain = $this->app->request->subDomain();
                $domain = $this->app->request->host(true);

                if (isset($bind[$domain])) {
                    $appName = $bind[$domain];
                    $this->app->http->setBind();
                } elseif (isset($bind[$subDomain])) {
                    $appName = $bind[$subDomain];
                    $this->app->http->setBind();
                } elseif (isset($bind['*'])) {
                    $appName = $bind['*'];
                    $this->app->http->setBind();
                }
            }

            if (!$this->app->http->isBind()) {
                $path = $this->app->request->pathinfo();
                $map = $this->app->config->get('app.app_map', []);
                $deny = $this->app->config->get('app.deny_app_list', []);

                $name = current(explode('/', $path));

                if (strpos($name, '.')) {
                    $name = strstr($name, '.', true);
                }

                if (isset($map[$name])) {
                    if ($map[$name] instanceof Closure) {
                        $result = call_user_func_array($map[$name], [$this->app]);
                        $appName = $result ?: $name;
                    } else {
                        $appName = $map[$name];
                    }
                } elseif ( $name && ( false !== array_search($name, $map) || in_array($name, $deny))) {
                    throw new HttpException(404, 'app not exists:' . $name);
                } elseif ($name && isset($map['*'])) {
                    $appName = $map['*'];
                } else {
                    $appName = $name ?: $defaultApp;
                    if (stripos($appName, 'plugins-') !== 0) {
                        if ($this->getAppPath($appName) === false) {
                            $express = $this->app->config->get('app.app_express', false);
                            if ($express) {
                                $this->setApp($defaultApp);
                                return true;
                            } else {
                                return false;
                            }
                        }
                    }
                }

                if ($name) {
                    $this->app->request->setRoot('/' . $name);
                    $this->app->request->setPathinfo(strpos($path, '/') ? ltrim(strstr($path, '/'), '/') : '');
                }
            }
        }
        $this->path = $this->app->http->getPath();
        $this->setApp($appName ?: $defaultApp);
        return true;
    }

    /**
     * 获取当前运行入口名称
     * @access protected
     * @codeCoverageIgnore
     * @return string
     */
    protected function getScriptName(): string
    {
        if (isset($_SERVER['SCRIPT_FILENAME'])) {
            $file = $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($_SERVER['argv'][0])) {
            $file = realpath($_SERVER['argv'][0]);
        }

        return isset($file) ? pathinfo($file, PATHINFO_FILENAME) : '';
    }

    /**
     * 设置应用
     * @param string $appName
     */
    protected function setApp(string $appName): void
    {

        $nameSpace = $this->app->config->get('app.app_namespace') ?: 'app';
        $this->app->http->name($appName);

        $appPath = $this->getAppPath($appName);

        if (stripos($appName, 'plugins-') === 0 || stripos($appPath, 'plugins') !== false) {
            // 插件应用
            $appName = str_replace("plugins-","",$appName);
            $nameSpace = "plugins\\{$appName}";
            $appPath =  $this->getAppPath($appName);

        } else {
            $nameSpace = "{$nameSpace}\\{$appName}";
        }

        $this->appName = $appName;
        $this->app->setAppPath($appPath);
        $this->app->setNamespace($nameSpace);

        if (is_dir($appPath)) {
            $this->app->setRuntimePath($this->app->getRuntimePath() . $appName . DIRECTORY_SEPARATOR);
            $this->app->http->setRoutePath($this->getRoutePath());

            //加载应用
            $this->loadApp($appName, $appPath);
        }
    }


    /**
     * 根据appName 获取应用路径
     *
     * @param string $appName
     * @return bool|string
     */
    protected function getAppPath(string $appName)
    {
        $appPath = $this->path ?: $this->app->getBasePath() . $appName . DIRECTORY_SEPARATOR;
        if(is_dir($appPath)){
            return $appPath;
        }

        $pluginPath = $this->path ?: $this->app->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR . $appName . DIRECTORY_SEPARATOR;
        if(is_dir($pluginPath)){
            return $pluginPath;
        }
        return '';
    }

    /**
     * 加载应用文件
     * @param string $appName 应用名
     * @return void
     */
    protected function loadApp(string $appName, string $appPath): void
    {
        if (is_file($appPath . 'common.php')) {
            include_once $appPath . 'common.php';
        }

        $files = [];

        $files = array_merge($files, glob($appPath . 'config' . DIRECTORY_SEPARATOR . '*' . $this->app->getConfigExt()));

        foreach ($files as $file) {
            $this->app->config->load($file, pathinfo($file, PATHINFO_FILENAME));
        }

        if (is_file($appPath . 'event.php')) {
            $this->app->loadEvent(include $appPath . 'event.php');
        }

        if (is_file($appPath . 'middleware.php')) {
            $this->app->middleware->import(include $appPath . 'middleware.php', 'app');
        }

        if (is_file($appPath . 'provider.php')) {
            $this->app->bind(include $appPath . 'provider.php');
        }

        if (is_file($appPath . 'bootstrap.php')) {
            include_once $appPath . 'bootstrap.php';
        }

        // 加载应用默认语言包
        $this->app->loadLangPack($this->app->lang->defaultLangSet());
        $path = $this->app->getAppPath() . 'lang' . DIRECTORY_SEPARATOR . $this->app->lang->defaultLangSet() .DIRECTORY_SEPARATOR. '*.php';
        $files = glob($path);
        $this->app->lang->load($files);
    }

}
