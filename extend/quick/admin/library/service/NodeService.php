<?php
declare (strict_types=1);

namespace quick\admin\library\service;


use app\common\controller\Backend;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use quick\admin\Quick;
use quick\admin\QuickPluginService;
use quick\admin\Resource;
use quick\admin\Service;
use quick\admin\annotation\AdminAuth;
use ReflectionClass;
use ReflectionMethod;
use think\facade\Lang;
use think\helper\Str;

/**
 * Class NodeService
 * @package quick\admin\library\service
 */
class NodeService extends Service
{

    const SYSTEM_AUTH_NODE = 'system_auth_node';
    /**
     * @var AnnotationReader
     */
    protected $reader;


    public function initialize(): Service
    {

        $this->reader = app(Reader::class);

        return parent::initialize(); // TODO: Change the autogenerated stub
    }

    /**
     * 获取当前节点内容
     * @param string $type
     * @return string
     */
    public function getCurrent(string $type = ''): string
    {
        $space = $this->app->getNamespace();
        $prefix = strtolower($this->app->http->getName());
        if (preg_match("|\\\\plugins\\\\{$prefix}$|", $space)) {
            $prefix = "plugins-{$prefix}";
        }

        // 获取应用前缀节点
        if ($type === 'module') {
            return $prefix;
        }
        // 获取控制器前缀节点
        $route = $this->app->request->route();
        $controller = $this->app->request->controller();
        $strArr = explode('.', $controller);
        foreach ($strArr as $k => $str) {
            $strArr[$k] = Str::snake($str);
        }
        $controller = implode('/', $strArr);

        $middle = isset($route['resource']) ? 'resource/' . $route['resource'] : Str::snake($controller);
//        echo  $prefix . '/' . $middle;
        if ($type === 'controller') {
            return $prefix . '/' . $middle;
        }

        // 获取完整的权限节点
        if (isset($route['resource'])) {
            return $prefix . '/' . $middle . '/' . $route['action'];
        }
        return $prefix . '/' . $middle . '/' . strtolower($this->app->request->action());
    }

    /**
     * 检查并完整节点内容
     * @param null|string $node
     * @return string
     */
    public function fullNode(?string $node = ''): string
    {
        if (empty($node)) {
            return $this->getCurrent();
        }
        switch (count($attrs = explode('/', $node))) {
            case 2:
                $suffix = Str::snake($attrs[0]) . '/' . $attrs[1];
                return $this->getCurrent('module') . '/' . strtolower($suffix);
            case 1:
                return $this->getCurrent('controller') . '/' . strtolower($node);
            default:
                $attrs[1] = Str::snake($attrs[1]);
                return strtolower(join('/', $attrs));
        }
    }


    /**
     * 获取节点
     *
     * @param bool $force
     * @return array|mixed
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function getNodes($force = false)
    {

        $data = $this->app->cache->get(self::SYSTEM_AUTH_NODE, []);

        if ($force) {
            $data = $this->updateNodes();
        }

        return $data;
    }


    /**
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function updateNodes()
    {
        $data = $this->getNodeAll();
        $this->app->cache->set(self::SYSTEM_AUTH_NODE, $data);
        return $data;
    }


    /**
     * 获取菜单有效访问节点
     * @param bool $force
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function getMenuNodes($force = false)
    {
        $nodeList = $this->getNodes($force);
        $nodes = [];
        foreach ($nodeList as $item) {
            if ($item['level'] >= 3) {
                $nodes[] = [
                    'value' => $item['node'],
                    'lable' => $item['title'],
                ];
            }
        }
        return $nodes;
    }


    /**
     * 获取系统全部节点
     *
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function getNodeAll()
    {
        return array_merge(
            $this->controllerNodes(),
            $this->resourceNodes(),
            $this->menuNodes()
        );
    }


    /**
     * 扫描插件目录
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function scanPluginDirectory()
    {
        $plugins = PluginService::instance()->getPlugins();
        $files = [];
        foreach ($plugins as $plugin) {
            $files = array_merge($files, $this->scanDirectory(root_path("plugins/" . $plugin['name'])));
        }
        return $files;
    }


    /**
     * 控制器节点
     * @return array
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function controllerNodes(): array
    {
        static $data = [];


        $appList = $this->scanDirectory($this->app->getBasePath());
        $pluginList = $this->scanPluginDirectory();


        // 加载系统语言包
        $this->loadLang();


        $files = array_merge(
            $appList,
            $pluginList
        );

        $ignore = ["__construct", 'success', 'error', 'form', 'table'];
        foreach ($files as $file) {

            if (preg_match("|/(\w+)/(\w+)/controller/(.+)\.php$|i", $file, $matches)) {

                list(, $namespace, $application, $baseclass) = $matches;
                $classStr = strtr("{$namespace}/{$application}/controller/{$baseclass}", '/', '\\');

                if (!is_subclass_of($classStr, Backend::class)) {
                    continue;
                }
                $class = new \ReflectionClass($classStr);
                $baseclass = $this->snakeNode($baseclass);
                $prefix = strtolower(strtr("{$application}/" . $baseclass, '\\', '/'));
                $application = strtolower($application);

                $plugin_name = $application;
                //模块节点
                $data[$application] = [
                    "title" => __($application),
                    'mode' => 'controller',
                    'plugin_name' => __($plugin_name),
                    "node" => $application,
                    "pnode" => '',
                    "level" => 1,
                    "is_auth" => 1,
                    "is_menu" => 1,
                    "method" => [],
                    "is_login" => 1
                ];

                $data[$prefix] = $this->parseNode($class, $baseclass, $prefix, $application, 2, "controller", $plugin_name);

                foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                    if (in_array($method->getName(), $ignore)) {
                        continue;
                    }

                    $url_key = strtolower("{$prefix}/{$method->getName()}");
                    $data[$url_key] = $this->parseNode($method, $method->getName(), $url_key, $prefix, 3, "controller", $plugin_name);

                }
            }
        }

        return $data;
    }


    private function loadLang()
    {
        $langset = Lang::getLangSet();
        $files = glob($this->app->getBasePath() . 'lang' . DIRECTORY_SEPARATOR . $langset . '.*');
        $plugins = PluginService::instance()->getPlugins();
        foreach ($plugins as $plugin) {
            $tempFiles = glob(root_path("plugins" . DIRECTORY_SEPARATOR . $plugin['name']) . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $langset . '.*');
            $files = array_merge($files, $tempFiles);
        }
        Lang::load($files);
    }


    /**
     * @param string $nodeStr
     * @return string
     */
    protected function snakeNode(string $nodeStr)
    {
        $arr = explode("/", $nodeStr);
        foreach ($arr as $k => $item) {
            $arr[$k] = Str::snake($item);
        }
        return implode("/", $arr);
    }


    /**
     * 解析节点
     *
     * @param  $class
     * @param string $default
     * @param string $node
     * @param string $pnode
     * @param int $level
     * @param string $mode
     * @param string $plugin_name
     * @return array
     */
    protected function parseNode($class, string $default, string $node, string $pnode, int $level, string $mode = "controller", string $plugin_name)
    {
        if ($class instanceof ReflectionClass) {
            /** @var AdminAuth $classAnnotations */
            $classAnnotations = $this->reader->getClassAnnotation(
                $class, AdminAuth::class
            );
        }
        if ($class instanceof ReflectionMethod) {
            //方法节点
            $classAnnotations = $this->reader->getMethodAnnotation(
                $class, AdminAuth::class
            );
        }

        $nodeInfo = [
            'title' => __($default),
            'plugin_name' => $plugin_name,
            'is_auth' => 1,
            'is_menu' => 1,
            'is_login' => 1,
            "node" => $node,
            "pnode" => $pnode,
            "level" => $level,
            "method" => [],
            'mode' => $mode
        ];
        if ($classAnnotations instanceof AdminAuth) {
            $nodeInfo = array_merge($nodeInfo, [
                'title' => __($classAnnotations->getTitle($default)),
                'is_auth' => $classAnnotations->getAuth(),
                "node" => $classAnnotations->getNode($node),
                'is_menu' => $classAnnotations->getMenu(),
                'is_login' => $classAnnotations->getLogin(),
                "method" => $classAnnotations->getMethod()
            ]);
        }
        $nodeInfo['node'] = strtolower($nodeInfo['node']);
        $nodeInfo['pnode'] = strtolower($nodeInfo['pnode']);
        return $nodeInfo;
    }

    /**
     *  quick资源节点
     *
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    public function resourceNodes(): array
    {

        $appList = $this->scanDirectory($this->app->getBasePath());
        $pluginList = $this->scanPluginDirectory();

        $files = array_merge(
            $appList,
            $pluginList
        );
        foreach ($files as $file) {
            if (preg_match("|/(\w+)/(\w+)/Plugin.php$|i", $file, $matches)) {

                list(, $namespace, $application) = $matches;
                $classStr = strtr("{$namespace}/{$application}/Plugin", '/', '\\');
                if (!is_subclass_of($classStr, QuickPluginService::class)) {
                    continue;
                }
                /** @var QuickPluginService $quick */
                $quick = invoke($classStr);
                $quick->boot();
            }
        }

        $nodes = [];
        foreach (Quick::$resources as $plugin => $list) {
            //模块节点
            $nodes[$plugin] = [
                "title" => __($plugin),
                'plugin_name' => $plugin,
                'mode' => 'resource',
                "node" => strtolower($plugin),
                "pnode" => '',
                "level" => 1,
                "is_auth" => 1,
                "is_menu" => 1,
                "method" => [],
                "is_login" => 1,
            ];

            foreach ($list as $resource) {
                $list = $this->getResourceNodesByClass($resource, $plugin);
                $nodes = array_merge($nodes, $list);
            }
        }
        return $nodes;

    }


    /**
     *  获取一个资源类的所有访问node
     *
     * @param string $resourceClassStr
     * @param string $plugin
     * @return mixed
     * @throws \ReflectionException
     * @throws \think\Exception
     */
    protected function getResourceNodesByClass(string $resourceClassStr, string $plugin)
    {
        $ignore = ["__construct", 'getAllActions', 'handleResourceAction', 'newModel', 'uriKey', 'model', 'filter', 'createUrl'];
        $class = new \ReflectionClass($resourceClassStr);
        $data = [];
        if ($class->isAbstract() || !is_subclass_of($resourceClassStr, Resource::class)) {
            return $data;
        }


        /** @var Resource $resource */
        $resource = invoke($resourceClassStr);

        $resourceUriKey = $resource::uriKey();
        $prefix = strtolower(strtr("{$plugin}/resource/" . $resourceUriKey, '\\', '/'));


        //资源节点
        $data[$prefix] = $this->parseNode($class, $prefix, $prefix, $plugin, 2, 'resource', $plugin);

        //资源类方法节点
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (in_array($method->getName(), $ignore)) {
                continue;
            }
            //方法节点
            $url_key = strtolower("{$prefix}/{$method->getName()}");

            $data[$url_key] = $this->parseNode($method, $method->getName(), $url_key, $prefix, 3, 'resource', $plugin);
        }


        //资源类 动作节点
        try {
            $actions = $resource->getAllActions();
        }catch (\Throwable $e){
            throw new \Exception($e->getMessage().":".$resourceClassStr);
        }


        foreach ($actions as $key => $action) {
            $actionReflection = new \ReflectionClass($action);
            $url_key = strtolower("{$prefix}/{$key}");
            $data[$url_key] = $this->parseNode($actionReflection, $key, $url_key, $prefix, 3, 'resource', $plugin);
        }
        return $data;
    }


    /**
     * 菜单自定义节点
     *
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menuNodes()
    {
        $menus = $this->app->db->name('SystemMenu')->where("node", "<>", "")->select();
        $nodes = [];
        foreach ($menus as $menu) {
            $nodes[$menu['node']] = [
                "title" => __($menu['title']),
                "mode" => "custom",
                "plugin_name" => $menu['plugin_name'],
                "node" => $menu['node'],
                "pnode" => $menu['plugin_name'],
                "level" => 1,
                "is_auth" => 1,
                "is_menu" => 1,
                "is_login" => 1
            ];
        }


        return $nodes;

    }


    /**
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateNode()
    {

        $nodeAll = $this->getNodeAll();
        $menuNodes = $this->menuNodes();
        $nodeAll = array_merge($nodeAll, $menuNodes);

        $storeNodeAll = $this->app->db->name('SystemNode')->column('node', "node");
        $delList = array_diff_key($storeNodeAll, $nodeAll);
        $insertList = array_diff_key($nodeAll, $storeNodeAll);
        if (!empty($delList)) {
            $this->app->db->name('SystemNode')->where("node", "in", array_keys($delList))->delete();
        }
        if (!empty($insertList)) {
            $pnode = [];
            foreach ($insertList as $node) {
                if (!empty($node['pnode'])) {
                    $pnode[$node['node']] = $node['pnode'];
                }
                $this->app->db->name('SystemNode')->strict(false)->insert($node);
            }

        }
    }

    /**
     * 获取所有PHP文件列表
     * @param string $path 扫描目录
     * @param array $data 额外数据
     * @param null|string $ext 文件后缀
     * @return array
     */
    public function scanDirectory(string $path, array $data = [], ?string $ext = 'php'): array
    {
        if (file_exists($path)) if (is_file($path)) {
            $data[] = strtr($path, '\\', '/');
        } elseif (is_dir($path)) {
            foreach (scandir($path) as $item) if ($item[0] !== '.') {
                $real = rtrim($path, '\\/') . DIRECTORY_SEPARATOR . $item;

                if (strpos($item, 'node_modules') !== false || strpos($item, 'plugins') !== false || strpos($item, 'common') !== false) {
                    continue;
                }

                if (is_readable($real)) if (is_dir($real)) {
                    $data = $this->scanDirectory($real, $data, $ext);
                } elseif (is_file($real) && (is_null($ext) || pathinfo($real, 4) === $ext)) {
                    $data[] = strtr($real, '\\', '/');
                }
            }
        }
        return $data;
    }

}
