<?php
declare(strict_types=1);

// +----------------------------------------------------------------------
// | Library for QuickAdmin
// +----------------------------------------------------------------------
// | 此文件改造于https://gitee.com/zoujingli/ThinkLibrary开源项目，在此特别提出感谢 ！！！！！！！！！！！！！！！
// +----------------------------------------------------------------------
// | 原始源代码来自 https://gitee.com/zoujingli/ThinkLibrary ，基于 MIT 协议开源
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\common\controller\Backend;
use quick\admin\annotation\AdminAuth;
use quick\admin\library\service\ModuleService;
use quick\admin\library\service\SystemService;
use think\Request;

/**
 * Class Index
 * @AdminAuth(title="升级管理",auth=false,menu=false,login=false )
 * @package app\admin\controller
 */
class Update extends Backend
{


    /**
     * 访问环境拦截
     */
    protected function initialize()
    {
        if (!SystemService::instance()->checkRunMode()) {
            return $this->responseJson([
                'msg' => '只允许访问本地或官方代码！',
                'code' => 1,
            ]);
        }
    }

    /**
     * @AdminAuth(title="升级管理",auth=false,menu=false,login=false )
     * 读取文件内容
     */
    public function get()
    {
        $filename = decode(input('encode', '0'));
        if (!ModuleService::instance()->checkAllowDownload($filename)) {
            return $this->responseJson([
                'msg' => '下载的文件不在认证规则中！',
                'code' => 1,
            ]);
        }
        if (file_exists($realname = $this->app->getRootPath() . $filename)) {

            return $this->responseJson([
                'msg' => '读取文件内容成功！',
                'code' => 0,
                'data' => [
                    'content' => base64_encode(file_get_contents($realname)),
                ]
            ]);
        } else {
            return $this->responseJson([
                'msg' => '读取文件内容失败！',
                'code' => 1,
            ]);
        }
    }

    /**
     * @AdminAuth(title="升级管理",auth=false,menu=false,login=false )
     * 读取文件列表
     */
    public function node()
    {
        $routes = ModuleService::bind();
        $rules = [];
        $ignore = [];
        foreach ($routes as $bind) {
            $rules = array_merge($rules, $bind['rules']);
            $ignore = array_merge($ignore, $bind['ignore']);
        }
        $data = ModuleService::instance()->getChanges($rules, $ignore);

//        $data = ModuleService::instance()->getChanges(
//            json_decode($this->request->post('rules', '[]', ''), true),
//            json_decode($this->request->post('ignore', '[]', ''), true)
//        );
        return $this->responseJson($data);
    }


    /**
     * @AdminAuth(title="升级管理",auth=false,menu=false,login=false )
     */
//    public function update()
//    {
//        $data = ModuleService::instance()->installFile();
//        halt($data);
//    }

    /**
     * 获取模块信息
     * @AdminAuth(title="升级管理",auth=false,menu=false,login=false )
     */
    public function version()
    {
        return $this->responseJson(ModuleService::instance()->getModules());
    }

}
