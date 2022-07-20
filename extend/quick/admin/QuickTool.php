<?php
declare (strict_types=1);

namespace quick\admin;


use quick\admin\events\ServingQuick;
use think\helper\Str;

abstract class QuickTool
{

    use AuthorizedToSee;


    /**
     * 名称
     *
     * @var string
     */
    public $name = "quick";


    /**
     * @var string
     */
    protected $app_key = "tool";


    /**
     * 设置appkey
     *
     * @param string $app_key
     * @return $this
     */
    public function appKey(string $app_key)
    {
        $this->app_key = $app_key;
        return $this;
    }


    /**
     * @return string
     */
    public function getAppkey()
    {
        return $this->app_key;
    }

    /**
     * 资源的URI密钥
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::snake(class_basename(get_called_class()));
    }


    /**
     * 启动
     */
    public function boot()
    {

        $this->registerAssets();
        $resources = $this->resources();
        $key = $this->getAppKey();
        Quick::registerResource(function () use ($key, $resources) {
            Quick::resources($key, $resources);
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
        $module = self::uriKey();

        Quick::registerAssets(function (ServingQuick $event) use ($module, $scripts, $styles) {
            !empty($scripts) && Quick::script($module, $scripts);
            !empty($styles) && Quick::style($module, $styles);
        });

    }


    /**
     * @return array
     */
    abstract function script(): array;


    /**
     * @return array
     */
    abstract function style(): array;

    /**
     * @return array
     */
    public function resources(): array
    {
        return [];
    }

}
