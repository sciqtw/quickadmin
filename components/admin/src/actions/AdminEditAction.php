<?php
declare(strict_types=1);

namespace components\admin\src\actions;


use app\common\model\SystemAdminInfo;
use quick\admin\library\service\AuthService;
use quick\admin\annotation\AdminAuth;
use quick\admin\actions\Action;
use quick\admin\form\Form;

/**
 * 基本信息
 * @AdminAuth(auth=true,menu=true,login=true,title="基本信息")
 * @package app\admin\resource\example\actions
 */
class AdminEditAction extends Action
{



    protected function initAction()
    {
        $form = $this->form();
        $userId = app()->auth->getAdminId();
        if (empty($userId)) {
            return $this->response()->error('设置失败');
        }
        $userModel = SystemAdminInfo::find($userId);
        $form->resolve($userModel->toArray());
        $form->url($this->storeUrl([
            self::$keyName => $this->request->param(self::$keyName)
        ]));
        $this->display($form);
    }


    public function form()
    {

        $form = Form::make("设置")->labelWidth(150);

        $form->labelWidth(100);
        $form->text('name', "名称")->rules('require');
        $form->text('nickname', "昵称")->rules('require');
        $form->text('email', "邮箱")->rules('email');
        $form->text('phone', "手机")->rules('mobile');

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
        $userId = app()->auth->getAdminId();;
        if (empty($userId)) {
            return $this->response()->error('设置失败');
        }
        $userModel = SystemAdminInfo::find($userId);
        $userModel->nickname = $data['nickname'] ?? '';
        $userModel->phone = $data['phone'] ?? '';
        $userModel->email = $data['email'] ?? '';
        if ($userModel->save() !== false) {
            return $this->response()->success('设置成功', $data);
        }
        return $this->response()->error('设置失败');
    }


}
