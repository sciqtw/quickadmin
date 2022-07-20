<?php
declare(strict_types=1);

namespace app\admin\controller;


use app\common\controller\Backend;
use quick\admin\annotation\AdminAuth;
use quick\admin\http\response\JsonResponse;
use quick\admin\library\service\AuthService;
use quick\admin\library\service\NodeService;
use quick\admin\Quick;
use think\facade\Request;
use think\facade\Validate;

/**
 * @AdminAuth(auth=false,menu=true,login=true,title="登录系统")
 * @package app\admin\controller
 */
class Passport extends Backend
{


    /**
     * @AdminAuth(auth=false,menu=false,login=false,title="登录")
     * @return \think\response\Json
     * @throws \Exception
     */
    public function login()
    {

        $loginCaptcha = $this->app->session->get("LoginCaptcha", false);
        $rules = [
            'account|账号' => 'require|max:25',
            'password|密码' => 'require|min:6',
        ];
        if ($loginCaptcha) {
            $rules['captcha|验证码'] = 'require|captcha';
        }

        $validate = Validate::rule($rules);

        $data = Request::param();
        if (!$validate->check($data)) {
            return JsonResponse::make()->error($validate->getError())->send();
        }

        $auth = Quick::getAuthService();
        try {
            $res = $auth->login($data['account'], $data['password'], 8 * 60 * 60);
            if ($res) {
                $oplog = [
                    'node' => NodeService::instance()->getCurrent(),
                    'action' => 'login',
                    'content' => "登录系统",
                    'geoip' => $this->app->request->ip() ?: '127.0.0.1',
                    'username' => $auth->getUsername() ?: '-',
                ];
                $this->app->db->name('SystemOplog')->insert($oplog) !== false;
                $this->app->session->delete("LoginCaptcha");
                return JsonResponse::make()->success('success')->send();
            }

            $this->app->session->set("LoginCaptcha", true);
            return JsonResponse::make()->error($auth->getError())->send();

        }catch (\Exception $e){
            return JsonResponse::make()->error($e->getMessage(),[
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ])->send();
        }





    }


    /**
     * @AdminAuth(auth=false,menu=false,login=true,title="员工数据")
     * @return \think\response\Json
     * @throws \Exception
     */
    public function userInfo()
    {
        $auth = Quick::getAuthService();
        if ($auth->isLogin()) {

            $user = $auth->getUserInfo();
            $user['username'] = $user['name'];
            return json(["data" => $auth->getUserInfo(), "code" => 0]);
        }
        return json(["msg" => '请先登录', "code" => 419]);
    }


}
