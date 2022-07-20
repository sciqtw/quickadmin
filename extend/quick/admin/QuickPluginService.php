<?php
declare (strict_types=1);

namespace quick\admin;


class QuickPluginService
{
    use AuthorizedToSee;

    /**
     * 应用名称
     *
     * @var string
     */
    public $name = "quick";


    /**
     * 应用key
     *
     * @var string
     */
    public $app_key = "quick";

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->app_key;
    }

    /**
     *
     */
    public function boot()
    {
        Quick::$app_key = $this->getAppKey();
        $this->bootTools();
        $this->registerAssets();// 前端资源
        $this->registerResources();// 后端资源
    }


    /**
     * 第三方工具
     * @return array
     */
    public function tools(): array
    {
        return [];
    }


    /**
     *  注册script
     *
     * @return array
     */
    public function script(): array
    {

        return [];
    }


    /**
     * 注册style
     *
     * @return array
     */
    public function style(): array
    {
        return [];
    }


    /**
     * 本地组件路径
     * @return string
     */
    public function viewComponentPath()
    {
        return '';
    }


    /**
     * 渲染组件
     * @return bool
     */
    public function readerComponent()
    {
        if(!$this->viewComponentPath()){
            return false;
        }

        $config = [
            'view_path'	=>	root_path("plugins/{$this->getAppKey()}/view"),
            'cache_path'	=>	root_path('/runtime/template'),
            'view_suffix'   =>	'html',
        ];
        $template = new \think\Template($config);
        $template->fetch($this->viewComponentPath());
    }


    /**
     * @return array
     */
    public function resources():array
    {
        return [];
    }


    /**
     * 注册资源
     */
    public function registerResources()
    {

        $appKey = $this->getAppKey();
        $dir = root_path(str_replace([
            "Plugin",
            '/',
            '\\',
        ],[
            "quick",
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
        ],static::class));
        $resources = $this->resources();
        Quick::registerResource(function () use ($appKey,$resources,$dir){
            Quick::resources($appKey,$resources);
            Quick::resourcesIn($appKey,$dir);
        });

    }


    /**
     * 启动加载assets
     *
     */
    public function registerAssets()
    {

        $scripts = $this->script();
        $styles = $this->style();
        $module = $this->getAppKey();
        Quick::registerAssets(function () use ($module, $scripts, $styles) {
            !empty($scripts) && Quick::script($module, $scripts);
            !empty($styles) && Quick::style($module, $styles);
        });

    }



    /**
     *  启动第三方资源工具
     */
    private function bootTools()
    {
        if ($tools = $this->tools()) {
            collect($tools)->each(function (QuickTool $tool) {
                if ($tool->authorizedToSee(request())) {
                    $tool->appKey($this->getAppKey());
                    $tool->boot();
                }
            });
        }
    }


    /**
     * cli命令行及所有请求都会执行，谨慎使用，影响性能一般用于事件监听使用
     */
    public function runBoot()
    {

    }

}
