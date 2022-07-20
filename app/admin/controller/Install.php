<?php
declare(strict_types=1);

namespace app\admin\controller;


use app\common\service\common\InstallService;
use think\View;

/**
 * 安装
 */
class Install
{



    public function index(InstallService $service)
    {
        if (file_exists(app()->getRootPath() . '/install.lock')) {
            return  '只允许访问本地或官方代码！'.app()->getRootPath() . '/install.lock';
        }
        if(request()->isPost()){
            $service->setData(request()->post());
            return $service->install();
        }
        return view('index');
    }

}
