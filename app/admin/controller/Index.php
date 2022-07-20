<?php
declare(strict_types=1);

namespace app\admin\controller;


use app\admin\library\Auth;
use app\common\controller\Backend;
use components\admin\src\actions\AdminEditAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\form\Form;
use quick\admin\library\service\AuthService;
use quick\admin\library\service\MenuService;
use quick\admin\library\service\UploadService;
use quick\admin\Quick;


/**
 * Class Index
 * @AdminAuth(
 *     title="系统管理",
 *     auth=true,
 *     menu=true,
 *     login=false
 * )
 * @package app\admin\controller
 */
class Index extends Backend
{


    /**
     * @AdminAuth(auth=false,menu=false,login=false,title="Index")
     * @return \think\response\View
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function index()
    {

        $style = [
            'padding-left' => '20px',
            'padding-right' => '20px',
        ];
        $config = [
            'base' => request()->baseFile(),
            '_baseUrl' => '',
            'dashboard' => '/admin/resource/dashboard/index',
            'module' => "admin",// 多应用模块
            'single_module' => false,//后端域名绑定模块 单应用
            'resources' => [],
            'actions' => [
                Component::action(Component::custom('div')
                    ->content('开发文档')->style($style))
                    ->openInNewTab('https://doc.quickadmin.cn'),
                Component::action(Component::custom('div')
                    ->content('个人中心')->style($style))
                    ->push('/admin/resource/admin_setting/index'),
            ],
            'title' => sysConfig('app_name'),
            'appName' => sysConfig('app_name'),
            'appLogo' => sysConfig('app_logo'),
            'checkCaptcha' => $this->app->session->get("LoginCaptcha", false),
            'showCopyright' => true,
            'copyrightDates' => '2020-2022',
            'copyrightCompany' => sysConfig('app_name'),
            'copyrightWebsite' => sysConfig('website'),

            /**
             *
             * 导航栏模式
             * side 侧边栏模式（含主导航）
             * head 顶部模式
             * single 侧边栏模式（无主导航）
             */
            "menuMode" =>  'head',
        ];

        Quick::dispatchAssets();
        Quick::loadResource(["*"]);//注册资源
        Quick::provideToScript($config);

        return view('quick@quick/index', [
            'favicon' => '',
            'app_name' => sysConfig('app_name'),
            'base_url' => request()->baseFile() . '/' . app()->http->getName(),
            'assetsUrl' => request()->baseFile() . '/' . app()->http->getName(),
        ]);
    }

    /**
     * @AdminAuth(auth=true,menu=true,login=true,title="菜单")
     * @return \think\response\Json
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function menu()
    {
        $methods = MenuService::instance();

        $menus = $methods->getTreeMenus("admin", false);

        return json([
            'code' => 0,
            'data' => $menus
        ]);
    }


    /**
     * @AdminAuth(auth=true,menu=true,login=true,title="退出登录")
     * @return \think\response\Json
     * @throws \Exception
     */
    public function logout()
    {
        $auth = Quick::getAuthService();
        if ($auth->logout()) {
            return json([
                'code' => 0,
                'msg' => '注销成功',
            ]);
//            return redirect("login");
        }
        return json([
            'code' => 1,
            'msg' => '注销失败',
        ]);
    }

}
