<?php
declare(strict_types=1);

namespace components\admin\src\actions;


use app\common\model\SystemAdminInfo;
use app\common\model\SystemUser;
use quick\admin\actions\Action;
use quick\admin\annotation\AdminAuth;
use quick\admin\form\Form;
use quick\admin\library\service\AuthService;
use quick\admin\library\tools\CodeTools;
use think\Request;

/**
 * 设置密码
 * @AdminAuth(auth=true,menu=true,login=true,title="设置密码")
 * @package app\admin\resource\example\actions
 */
class AdminPasswordAction extends Action
{


    public function init()
    {
        $this->drawer();
        $this->name = "设置密码";
        $this->getDisplay()->type("text");
    }


    protected function initAction()
    {


        $this->withMeta([
            'title' => '设置密码',
            'desc' => '设置您的登录密码',
        ]);
    }


    public function load()
    {

        $userId = AuthService::instance()->getAdminId();
        if (empty($userId)) {
            return $this->response()->error('设置失败');
        }
        $userModel = SystemAdminInfo::find($userId);

        $form = $this->form();
        $form->url($this->storeUrl());
        $form->resolve($userModel);

        $form->props("extendData", [
            self::$keyName => $this->request->param(self::$keyName)
        ]);

        return $this->response()->success("success", $form);
    }


    public function form()
    {

        $form = Form::make("设置密码")->labelWidth(150);
//        $form->text("username", "登录用户账户")->disabled();
        $form->text("password", "新的登录密码")
            ->password()
            ->rules("min:6|max:32|require")->resolveUsing(function () {
                return '';
            });
        $form->text("repassword", "重复登录密码")->password()
            ->rules("min:6|max:32|require|confirm:password");
        return $form;
    }

    /**
     * @return mixed|\quick\admin\http\response\JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function store()
    {

        $form = $this->form();
        $data = $form->getSubmitData($this->request, 3);
        $userId = AuthService::instance()->getAdminId();
        if (empty($userId)) {
            return $this->response()->error('设置失败');
        }
        $adminInfo = SystemAdminInfo::find($userId);
        if (empty($adminInfo)) {
            return $this->response()->error('设置失败');
        }

        $adminInfo->user->salt = CodeTools::random(5, 2);
        $adminInfo->user->password = SystemUser::hashPassword($data['password'], $adminInfo->user->salt);
        if ($adminInfo->user->save() !== false) {
            return $this->response()->success('设置成功', []);
        }
        return $this->response()->error('设置失败');
    }


}
