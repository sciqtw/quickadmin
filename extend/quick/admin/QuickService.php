<?php
declare (strict_types=1);

namespace quick\admin;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use quick\admin\command\ActionCommand;
use quick\admin\command\InstallCommand;
use quick\admin\command\ModelCommand;
use quick\admin\command\PluginCommand;
use quick\admin\command\QueueCommand;
use quick\admin\command\ResourceCommand;
use quick\admin\library\service\PluginService;
use quick\admin\multiple\MultiApp;
use quick\admin\multiple\Url;
use think\App;
use think\Cache;
use think\Config;
use think\facade\Lang;
use think\Service;

class QuickService extends Service
{


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app->event->listen('HttpRun', function () {
            $this->app->middleware->add(MultiApp::class);
            $this->app->middleware->add(function ($request, \Closure $next) {
                // 获取模块启动文件
                $path = app_path() . "QuickService.php";
                if (!is_file($path)) {
                    $path = app_path() . "Plugin.php";
                }

                if (is_file($path)) {
                    $resource = str_replace(
                        ['/', '.php'],
                        ['\\', ''],
                        strAfter($path, root_path())
                    );
                    try {
                        /** @var QuickPluginService $service */
                        invoke($resource)->boot();
                    } catch (\Exception $e) {

                    }
                }
                return $next($request);
            });
        });

        $this->registerCommand();

        $this->app->bind([
            'think\route\Url' => Url::class,
        ]);


        $this->loadPlugin();
//        Quick::script('price_tracker', __DIR__ . '/../dist/js/tool.js');
//        Quick::style('price_tracker', __DIR__ . '/../dist/css/tool.css');
        if (is_file(__DIR__ . '/helper.php')) {
            include_once __DIR__ . '/helper.php';
        }

    }


    public function loadPlugin()
    {
//        PluginService::instance()->bootPlugins();
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


        AnnotationReader::addGlobalIgnoredName('mixin');
        // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');

        $this->app->bind(Reader::class, function (App $app, Config $config, Cache $cache) {

            $store = $config->get('annotation.store');

            return new CachedReader(new AnnotationReader(), $cache->store($store), $app->isDebug());
        });


        $this->app->bind('JsonResponse', 'quick\\response\\JsonResponse');
//        $this->app->bind("adminAuth", 'quick\\admin\\library\\service\\AuthService');

        Lang::load(__DIR__ . '/lang/zh-cn.php', 'zh-cn');
        Lang::load(__DIR__ . '/lang/en-us.php', 'en-us');
        $this->loadRoutesFrom(__DIR__ . '/http/route/route.php');
        $this->registerJsonVariables();


    }


    /**
     * 注册quick json variables
     */
    protected function registerJsonVariables()
    {
        Quick::registerAssets(function () {
            //注册默认多语言配置
            $lang = config('quick.default_lang') ? config('quick.default_lang') : "zh-cn";
            Quick::translations(
                root_path('app/common/lang/quick') . $lang . ".json"
            );

            Quick::provideToScript([
                'timezone' => 'timezone',
                'lang' => config('quick.default_lang'),
                'translations' => Quick::allTranslations(),
                'locale' => 'locale',
            ]);
        });
    }



    /**
     *
     */
    protected function registerCommand()
    {
        $this->commands([
            ActionCommand::class,
            InstallCommand::class,
            ModelCommand::class,
            QueueCommand::class,
            ResourceCommand::class,
            PluginCommand::class,
        ]);
    }


}
