<?php
declare (strict_types=1);

namespace quick\admin;


use quick\admin\events\ServingQuick;
use quick\admin\library\service\AuthService;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use think\facade\Config;
use think\facade\Event;
use think\helper\Arr;
use think\helper\Str;
use think\Request;


class Quick
{

    /**
     * 加载资源
     */
    const EVENT_RESOURCE = "quick_event_resource";

    /**
     * 加载前端资源
     */
    const EVENT_ASSETS = "quick_event_assets";


    /**
     * 所有注册组件的js.
     *
     * @var array
     */
    public static $scripts = [];

    /**
     * 所有注册组件的样式.
     *
     * @var array
     */
    public static $styles = [];


    /**
     * 注册的资源名.
     *
     * @var array
     */
    public static $resources = [];


    /**
     * @var array
     */
    public static $tools = [];



    /**
     * 应该在nova javascript对象上提供的变量
     * @var array
     */
    public static $jsonVariables = [];


    /**
     * 本地化多语言
     * @var array
     */
    public static $translations = [];


    /**
     * 服务
     * @var array
     */
    protected static $services = [];


    /**
     * 获取当前版本号.
     *
     * @return string
     */
    public static function version()
    {
        return '1.0.0';
    }


    /**
     * 应用key
     *
     * @var string
     */
    public static $app_key = 'admin';


    /**
     * 加载资源
     * @var array $resourcesKeys
     */
    private static $loadResource = [];


    /**
     * @param array $resourcesKeys
     * @return static
     */
    public static function loadResource(array $resourcesKeys)
    {
        self::$loadResource = array_merge(self::$loadResource, $resourcesKeys);
        return new static;
    }


    /**
     * 获取应该名称
     *
     * @return string
     */
    public static function name()
    {
        return config('quick.name', 'Quick Site');
    }


    /**
     * 应用标识
     *
     * @return string
     */
    public static function appKey()
    {
        return self::$app_key;
    }


    /**
     * @return mixed
     */
    public static function path()
    {
        return config('quick.path', '/quick');
    }


    /**
     * 将给定值人性化为一个适当的名称
     * @param $value
     * @return string
     */
    public static function humanize($value)
    {
        if (is_object($value)) {
            return static::humanize(class_basename(get_class($value)));
        }
        return Str::title(Str::snake($value, ' '));;
    }


    /**
     * 监听加载resource事件
     *
     * @param \Closure $callback
     * @return static
     */
    public static function registerResource(\Closure $callback)
    {
        return self::listen(self::EVENT_RESOURCE,$callback);
    }


    /**
     * @return mixed
     */
    public static function dispatchResource()
    {
        return event(self::EVENT_RESOURCE);
    }


    /**
     * 监听加载assets事件
     *
     * @param \Closure $callback
     * @return static
     */
    public static function registerAssets(\Closure $callback)
    {
        return self::listen(self::EVENT_ASSETS,$callback);
    }


    /**
     * @return mixed
     */
    public static function dispatchAssets()
    {
        return event(self::EVENT_ASSETS);
    }



    /**
     * @param string $event
     * @param $callback
     * @return static
     */
    public static function listen(string $event, $callback)
    {
        Event::listen($event, $callback);
        return new static;
    }


    /**
     * 注册一个新工具组件
     * @param array $tools
     * @return static
     */
    public static function tools(array $tools)
    {
        static::$tools = array_merge(
            static::$tools,
            $tools
        );
        return new static;
    }


    /**
     * 启动可用的quick工具。
     * @param Request $request
     */
    public static function bootTools(Request $request)
    {
        collect(static::availableTools($request))->each(function ($tool) {
            $tool->boot();
        });
    }

    /**
     * 把工具注册到Quick
     * @param Request $request
     * @return mixed
     */
    public static function availableTools(Request $request)
    {
        //todo 工具权限过滤
//        return collect(static::$tools)->filter->authorize($request)->all();
        return static::$tools;
    }


    /**
     * 获取工具组件集合
     *
     * @return array
     */
    public static function registeredTools()
    {
        return static::$tools;
    }


    /**
     * 获取应该提供给全局quick javascript对象的json变量
     *
     * @param Request $request
     * @return array
     */
    public static function jsonVariables(Request $request)
    {
        return collect(static::$jsonVariables)->map(function ($variable) use ($request) {
            return is_callable($variable) ? $variable($request) : $variable;
        })->all();
    }

    /**
     * 设置quick全局json数据
     *
     * @param array $variables
     * @return static
     */
    public static function provideToScript(array $variables)
    {
        if (empty(static::$jsonVariables)) {
            static::$jsonVariables = [
                'base' => static::path(),
            ];
        }

        static::$jsonVariables = array_merge(static::$jsonVariables, $variables);

        return new static;
    }


    /**
     * 获取所有应该注册的可用脚script
     *
     * @param Request $request
     * @return array
     */
    public static function availableScripts(Request $request)
    {
        return self::getScriptsByKeys(self::$loadResource);
    }


    /**
     * 获取所有应该注册的可用style
     *
     * @param Request $request
     * @return array
     */
    public static function availableStyles(Request $request)
    {

        return self::getStylesByKeys(self::$loadResource);
    }


    /**
     *  获取所有scripts
     *
     * @return array
     */
    public static function allScripts()
    {
        return Arr::dot(static::$scripts);
    }


    /**
     * 获取所有styles
     *
     * @return array
     */
    public static function allStyles()
    {
        return Arr::dot(static::$styles);
    }


    /**
     *  根据keys 获取scripts
     *
     * @param array $keys
     * @return array
     */
    public static function getScriptsByKeys(array $keys)
    {

        list($list, $likes, $scripts) = [[], [], self::allScripts()];
        if (in_array("*", $keys)) {
            return $scripts;
        }
        foreach ($keys as $key) {
            if (strpos($key, '.*')) {
                $likes[] = str_replace(".*", "", $key);
            }
        }
        foreach ($scripts as $k => $script) {
            if (in_array($k, $keys)) {
                $list[$k] = $script;
            } else {
                foreach ($likes as $likeKey) {
                    if (strpos($k, $likeKey) !== false) {
                        $list[$k] = $script;
                    }
                }
            }
        }
        return $list;
    }


    /**
     *  根据keys 获取scripts
     *
     * @param array $keys
     * @return array
     */
    public static function getStylesByKeys(array $keys)
    {

        list($list, $likes, $styles) = [[], [], self::allStyles()];
        if (in_array("*", $keys)) {
            return $styles;
        }
        foreach ($keys as $key) {
            if (strpos($key, '.*')) {
                $likes[] = str_replace(".*", "", $key);
            }
        }
        foreach ($styles as $k => $style) {
            if (in_array($k, $keys)) {
                $list[$k] = $style;
            } else {
                foreach ($likes as $likeKey) {
                    if (strpos($k, $likeKey) !== false) {
                        $list[$k] = $style;
                    }
                }
            }
        }
        return $list;
    }


    /**
     * 注册添加一个js文件到quick
     *
     * @param string $name
     * @param string|array $path
     * @return static
     */
    public static function script(string $name, $path)
    {
        if (is_array($path)) {
            $data = static::$scripts[$name] ?? [];
            $path = array_merge($data, $path);
        }
        static::$scripts[$name] = $path;

        return new static;
    }


    /**
     * 注册添加一个css文件到quick
     *
     * @param string $name
     * @param string|array $path
     * @return static
     */
    public static function style(string $name, $path)
    {
        if (is_array($path)) {
            $data = static::$scripts[$name] ?? [];
            $path = array_merge($data, $path);
        }
        static::$styles[$name] = $path;
        return new static;
    }


    /**
     * @param $translations
     * @return static
     */
    public static function translations($translations)
    {

        if (is_string($translations)) {
            if (!is_readable($translations)) {
                return new static;
            }

            $translations = json_decode(file_get_contents($translations), true);
        }
        static::$translations = array_merge(static::$translations, $translations);

        return new static;
    }

    /**
     * Get all of the additional stylesheets that should be loaded.
     *
     * @return array
     */
    public static function allTranslations()
    {
        return static::$translations;
    }


    /**
     * @param string $module
     * @param $key
     * @return mixed
     */
    public static function resourceForKey(string $module, $key)
    {
        $resource = static::$resources[$module] ?? [];
        return collect($resource)->first(function ($value) use ($key) {
            return $value::uriKey() === $key;
        });
    }


    /**
     * 注册资源
     *
     * @param string $module
     * @param array $resources
     * @return static
     */
    public static function resources(string $module, array $resources)
    {
        static::$resources[$module] = array_unique(
            array_merge(static::$resources[$module] ?? [], $resources)
        );

        return new static;
    }


    /**
     * 注册给定目录中的所有资源类
     *
     * @param string $module
     * @param string $directory
     * @return array
     * @throws \ReflectionException
     */
    public static function resourcesIn(string $module, string $directory)
    {

        $cacheKey = md5("_resource_" . $module . $directory);
        $resources = (array)app()->cache->get($cacheKey, []);

        if (!app()->isDebug() && app()->cache->has($cacheKey)) {

            static::resources($module, collect($resources)->sort()->all());
            return $resources;
        }

        $resources = [];

        foreach ((new Finder)->in($directory)->files() as $resource) {

            $resource = str_replace(
                ['/', '.php'],
                ['\\', ''],
                strAfter($resource->getPathname(),
                    root_path()
                )
            );

            if (is_subclass_of($resource, Resource::class) &&
                !(new ReflectionClass($resource))->isAbstract()) {
                $resources[] = $resource;
            }
        }

        app()->cache->set($cacheKey, $resources);
        static::resources($module, collect($resources)->sort()->all());

        return $resources;
    }


    /**
     *  注册服务
     * @param array $services
     */
    public static function registerService(array $services)
    {
        self::$services = array_merge(self::$services, $services);
    }


    /**
     *  获取服务
     *
     * @return array
     */
    public static function services(): array
    {
        return self::$services;
    }


    /**
     * 加载输出style
     * @return \think\Response
     */
    public static function loadStyles()
    {
        $path = Arr::get(self::allStyles(), app()->request->param('name'));

        if (is_null($path)) {
            quick_abort(404, '找不到资源');
        }
        return response(
            file_get_contents($path),
            200,
            [
                'Content-Type' => 'text/css',
            ]
        )->lastModified(date('U', filemtime($path)));
    }


    /**
     * 加载输出script
     *
     * @return \think\Response
     * @throws Exception
     */
    public static function loadScript()
    {

        $path = Arr::get(self::allScripts(), app()->request->param('name'));

        if (is_null($path)) {
            quick_abort(404, '找不到资源');
        }

        return response(
            file_get_contents($path),
            200,
            [
                'Content-Type' => 'application/javascript',
            ]
        )->lastModified(date('U', filemtime($path)));
    }


    /**
     * @return AuthService
     * @throws \Exception
     */
    public static function getAuthService()
    {
        $authConfig = Config::get('quick.auth');
        if(!empty($authConfig['service'])){
            /** @var AuthService $service */
            $service = invoke($authConfig['service']);
            $service->scope = $authConfig['scope_key'] ?? 'admin';
            app()->auth = $service;
           return $service;
        }
        throw new \Exception('AuthService not setting');
    }
}
