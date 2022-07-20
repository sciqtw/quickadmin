<?php


//use quick\admin\http\middleware\AdminAuthMiddleware;

use quick\admin\library\service\AuthService;

return [
    'default_lang'    => env('lang.default_lang', 'zh-cn'),
    // 应用的命名空间
    'app_namespace' => '',
    'app_key' => 'admin',
    'app_name' => 'QuickAdmin',
    'auth' => [
        'scope_key' => 'admin',
        'service' => AuthService::class,
    ],
    //中间件
    'middleware' => [
//        AdminAuthMiddleware::class,
    ],
    'backup_global_files' => true,// 插件禁用是否备份全局文件
    'plugin_mode' => true,// 插件纯净模式 开启后插件安装完删除插件目录里面的全局文件
];
