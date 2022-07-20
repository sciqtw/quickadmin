<?php
declare(strict_types=1);

namespace components\admin\src\actions\auth;


use quick\admin\library\tools\CodeTools;
use quick\admin\annotation\AdminAuth;
use quick\admin\actions\RowAction;
use app\common\model\SystemUser;
use quick\admin\form\Form;
use think\Request;

/**
 * 设置密码
 * @AdminAuth(title="设置密码",auth=true,login=true,menu=false)
 * @package app\admin\resource\example\actions
 */
class EditUserPasswordAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemAdminInfo";


    public function init()
    {
        $this->getDisplay()->type("text");
        $this->name = "设置密码";
        $this->dialog();
    }


    public function form()
    {
        $form = Form::make("设置密码")->labelWidth(150);
//        $form->text("user.username", "登录用户账户")->disabled();
        $form->text("password", "新的登录密码")
            ->password()
            ->rules("min:6|max:32|require")->resolveUsing(function (){
                return '';
            });
        $form->text("repassword", "重复登录密码")->password()
            ->rules("min:6|max:32|require|confirm:password");
        return $form;
    }

    public function resolve($request,$model)
    {
        $form = $this->form();
        $form->url($this->storeUrl());
        $form->resolve($model);

        $form->props("extendData", [
            self::$keyName => $this->request->param(self::$keyName)
        ]);

        return $this->response()->success("success",$form);
    }

    public function handle($model,Request $request)
    {
        $form = $this->form();
        $data = (array)$form->getSubmitData($request, 3);
        $update['salt'] = CodeTools::random(5,2);
        $update['password'] = SystemUser::hashPassword($data['password'],$update['salt']);
        if($model->user->save($update)){
            $response = $this->response()->success($update['salt']."设置成功".$update['password']);
        }else{
            $response = $this->response()->error("设置失败");
        }
        return $response;
    }


}
