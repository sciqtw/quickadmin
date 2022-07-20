<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace quick\admin\multiple;

use think\Service as BaseService;

class App extends BaseService
{
    public function boot()
    {
        $this->app->event->listen('HttpRun', function () {
            $this->app->middleware->add(MultiApp::class);
        });

        $this->commands([
            'build' => command\Build::class,
            'clear' => command\Clear::class,
        ]);

        $this->app->bind([
            'think\route\Url' => Url::class,
        ]);
//        $this->load();
    }

    public function load()
    {
        foreach (glob($this->getAddonsPath() . '*/*.php') as $addons_file) {
            // 格式化路径信息
            $info = pathinfo($addons_file);
            // 获取插件目录名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) === 'plugin') {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\" . $info['filename']);
                // 跟插件基类方法做比对，得到差异结果
//                $hooks = array_diff($methods, $base);
//                // 循环将钩子方法写入配置中
//                foreach ($hooks as $hook) {
//                    if (!isset($config['hooks'][$hook])) {
//                        $config['hooks'][$hook] = [];
//                    }
//                    // 兼容手动配置项
//                    if (is_string($config['hooks'][$hook])) {
//                        $config['hooks'][$hook] = explode(',', $config['hooks'][$hook]);
//                    }
//                    if (!in_array($name, $config['hooks'][$hook])) {
//                        $config['hooks'][$hook][] = $name;
//                    }
//                }
            }
        }
    }

    /**
     * 获取 addons 路径
     * @return string
     */
    public function getAddonsPath()
    {
        // 初始化插件目录
        $addons_path = $this->app->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR;
        // 如果插件目录不存在则创建
        if (!is_dir($addons_path)) {
            @mkdir($addons_path, 0755, true);
        }

        return $addons_path;
    }

}
