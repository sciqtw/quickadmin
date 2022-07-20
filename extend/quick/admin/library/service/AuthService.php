<?php
declare(strict_types=1);

namespace quick\admin\library\service;


use app\common\model\SystemAdminInfo;
use app\common\model\SystemAuth;
use app\common\model\SystemAuthNode;
use app\common\model\SystemUser;
use Doctrine\Common\Annotations\AnnotationReader;
use quick\admin\contracts\AuthInterface;
use quick\admin\http\response\JsonResponse;
use quick\admin\Service;
use think\facade\Config;
use think\facade\Route;
use think\facade\Session;
use think\Request;

class AuthService extends Service implements AuthInterface
{

    /**
     *  错误信息
     * @var
     */
    public $error;

    /**
     * @var bool|SystemUser
     */
    protected $user = false;

    /**
     * @var
     */
    public $token;

    /**
     * @var string 权限域
     */
    public  $scope = 'admin';

    /**
     * @var array
     */
    public $nodes = [];

    /**
     * @return Service
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function initialize(): Service
    {
        $this->initUser();
        return $this;
    }

    /**
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initUser()
    {
        $admin_id = $this->getAdminId();
        if (!$admin_id) {
            return $this;
        }

        $userModel = SystemAdminInfo::with(['user', 'identity'])->hidden(['password', 'salt'])->find($admin_id);
        if (!$userModel || !$userModel->user->status) {
            return $this;
        }


//        if (Config::get('quick.login_unique') && md5($this->token) != $userModel['token']) {
//            $this->app->session->delete("user");
//            return $this;
//        }
//
//        if (Config::get('quick.loginip_check')) {
//            if (!isset($userModel['loginip']) || $userModel['loginip'] != request()->ip()) {
//                $this->app->session->delete("user");
//                return $this;
//            }
//        }
        $this->user = $userModel;

        if (app()->isDebug()) {
            $nodes = $this->_getAdminNodes($userModel);
        } else {
            $nodes = $this->_adminInfo("nodes", []);
        }
        $this->nodes = $nodes;


    }


    /**
     * 获取管理员权限节点
     *
     * @param SystemAdminInfo $admin
     * @return array
     */
    protected function _getAdminNodes(SystemAdminInfo $admin)
    {
        if (!empty($admin['auth_set']) && $auth_ids = explode(",", $admin['auth_set'])) {
            $authIds = SystemAuth::where("id", "in", $auth_ids)->where('status', 1)->column("id");
            if (!empty($authIds)) {
                return SystemAuthNode::where("auth", "in", $authIds)->column("node");
            }
        }
        return [];
    }


    /**
     * 登录
     *
     * @param $username
     * @param $password
     * @param int $keepTime
     * @return bool|mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login($username, $password, $keepTime = 0)
    {


        $user = SystemUser::where([
            'username' => $username,
        ])->find();
        if (empty($user)) {
            $this->setError("账户密码输入有误");
            return false;
        }

        $admin = SystemAdminInfo::where(["user_id" => $user->id, 'plugin_name' => $this->scope])->find();
        if (empty($admin)) {
            $this->setError("账户密码输入有误");
            return false;
        }

        if (Config::get('quickadmin.login_fail') && $user->login_fail_num >= 10 && time() - strtotime($user->updated_at) < 86400) {
            $this->setError('请一天后再登录');
            return false;
        }

        if ($user->password !== SystemUser::hashPassword($password, $user->salt)) {
            $admin->login_fail_num++;
            $admin->save();
            $this->setError('账户密码输入有误');
            return false;
        }

        if (!$user->status || !$admin->status) {
            $this->setError('用户已经禁用');
            return false;
        }

        $user->save([
            'login_at' => date("Y-m-d H:i:s"),
            'login_ip' => request()->ip(),
        ]);

        $nodes = $this->_getAdminNodes($admin);
        $admin = $admin->toArray();
        $admin['user'] = $user->hidden(['password', 'salt'])->toArray();
        $admin = array_merge($admin, ['nodes' => $nodes]);
        Session::set($this->getAuthScope(), $admin);
        $this->user = $admin;
        return true;
    }


    /**
     * @return bool|mixed
     */
    public function logout()
    {
        app()->session->delete($this->getAuthScope());
        app()->cookie->delete("keep_login");
        return true;
    }


    /**
     * 获取后台用户ID
     * @return integer
     */
    public function getAdminId(): int
    {
        return intval($this->_adminInfo("id", 0));
    }


    /**
     * 获取后台用户名称
     * @return string
     */
    public function getUsername(): string
    {
        return $this->user['name'] ?? '';
    }


    /**
     * @param string $field
     * @param string $default
     * @return string
     */
    protected function _adminInfo(string $field = '', $default = '')
    {
        $app_key = $this->getAuthScope();
        $field && $app_key .= "." . $field;
        return $this->app->session->get($app_key, '') ?: $default;
    }


    /**
     * @return SystemUser|bool
     */
    public function getUserInfo()
    {
        if (!$this->isLogin()) {
            return false;
        }
        return $this->user;
    }


    /**
     * 是否已经登录
     * @return boolean
     */
    public function isLogin(): bool
    {
        return $this->user !== false;
    }


    /**
     * 是否为超级用户
     *
     * @return bool
     */
    public function isSuper(): bool
    {

        if (!$this->isLogin()) {
            return false;
        }

        if ($this->user->identity->is_admin == 1 && $this->user->identity->is_super_admin == 1) {
            return true;
        }
        return false;
    }


    /**
     * 菜单节点检查
     *
     * @param string $node
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check(string $node): bool
    {


        if ($this->isSuper()) {
            return true;
        }

        $nodes = $this->nodes();
        if (!empty($nodes) && in_array($node, $nodes)) {
            return true;
        }
        return false;
    }


    /**
     * @param Object $object
     * @param string $action
     * @param Request $request
     * @return bool|\think\response\Json|\think\response\Redirect
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkAuth(Object $object, string $action, Request $request)
    {
        $module = 'admin';
        if ($this->checkAuthByObject($object, $action)) {
            return true;
        }

        if ($this->isLogin()) {
            if ($request->isAjax()) {
                return JsonResponse::make()->error('你无权操作', [
//                    $this->getUserInfo(),
                ], 413)->send();
            }
            return redirect((string)Route::buildUrl($module . '/index/index'));
        }

        if ($request->isAjax()) {
            return JsonResponse::make()->error('请先登录获取权限', [
                'redirect' => (string)Route::buildUrl($module . '/index/login')
            ], 419)->send();
        }
        return redirect((string)Route::buildUrl($module . '/index/index'));

    }


    /**
     * @param Object $object
     * @param string $action
     * @return bool
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkAuthByObject(Object $object, string $action = '')
    {

        if ($this->isSuper()) {
            return true;
        }

        $class = new \ReflectionClass($object);
        $reader = new AnnotationReader();

        if (!empty($action)) {
            $method = $class->getMethod($action);
            /** @var \quick\admin\annotation\AdminAuth $annotation */
            $annotation = $reader->getMethodAnnotation($method, \quick\admin\annotation\AdminAuth::class);
        } else {
            $annotation = $reader->getClassAnnotation($class, \quick\admin\annotation\AdminAuth::class);
        }


        if (!$annotation) {
            return false;
        }


        $methods = $annotation->getMethod();
        if (!empty($methods) && !in_array(request()->method(), $methods)) {
            // 请求类型不在权限控制内
            return true;
        }

        if (!$annotation->getLogin()) {
            return true;
        }

        if (!$this->isLogin()) {
            return false;
        }

        if (!$annotation->getAuth()) {
            return true;
        }

        $service = NodeService::instance();
        $node = $service->fullNode();
        $node = $annotation->getNode($node);


        $nodes = $this->nodes();
        if (!empty($nodes) && in_array($node, $nodes)) {
            return true;
        }
        return false;
    }


    /**
     * @return array
     */
    public function nodes(): array
    {
        if (!$this->isLogin()) {
            return [];
        }
        return $this->nodes;
    }


    /**
     * @return string
     */
    private function getAuthScope()
    {
        return $this->scope . "_user";
    }


    /**
     *  设置错误信息
     * @param $error
     * @return $this
     */
    public function setError(String $error)
    {
        $this->error = $error;
        return $this;
    }


    /**
     * 获取错误信息
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }


    public function __call($name, $arguments)
    {
        if(isset($this->{$name})){
            return $this->{$name};
        }
        $userInfo = $this->getUserInfo();
        return $userInfo ? data_get($userInfo, $name, '') : '';
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        $userInfo = $this->getUserInfo();
        return $userInfo ? data_get($userInfo, $name, '') : '';
    }

}
