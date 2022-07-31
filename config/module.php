<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------

return  [
    'serve' => "",
    'config' => [
        'admin'  => [
            'rules'  => [
                'app/admin',
                'app/common',
                'app/common.php',
                'components/admin',
                'components/vcharts',
            ],
            'ignore' => [],
        ],
        'plugins'  => [
            'rules'  => [
                'plugins/Plugin.php',
            ],
            'ignore' => [],
        ],
        'quick'  => [
            'rules'  => [ 'extend/quick'],
            'ignore' => [],
        ],
        'config' => [
            'rules'  => [
                'config/app.php',
                'config/log.php',
                'config/route.php',
                'config/trace.php',
                'config/view.php',
                'public/index.php',
                'public/router.php',
            ],
            'ignore' => [],
        ],
        'static' => [
            'rules'  => [
                'public/vue3',
                'public/assets/unpkg',
            ],
            'ignore' => [],
        ],
        'view' => [
            'rules'  => [
                'view/quick',
            ],
            'ignore' => [],
        ],
    ]
];
