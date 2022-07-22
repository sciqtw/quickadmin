<?php
declare(strict_types=1);

namespace components\admin\src\auth;

use app\common\model\SystemUser;
use app\common\model\SystemUserIdentity;
use components\admin\src\actions\auth\EditUserPasswordAction;
use quick\admin\library\tools\CodeTools;
use quick\admin\library\tools\TreeArray;
use quick\admin\annotation\AdminAuth;
use quick\admin\filter\Filter;
use quick\admin\Form\Form;
use quick\admin\Resource;
use quick\admin\table\Table;
use think\Request;

/**
 * Class Admin
 * @AdminAuth(title="系统管理员",auth=true,login=true,menu=true)
 * @package components\admin\src\auth
 */
class Admin extends Resource
{
    /**
     * @var string
     */
    protected $title = '系统员工';

    /**
     * @var string
     */
    protected $description = "系统员工管理";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemAdminInfo";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["username"];


    /**
     * 过滤器
     *
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth(100);
        $filter->like("nickname", "用户昵称")->width(12);

        return $filter;
    }


    public function model($model)
    {
        $model->where([
            ["plugin_name", "=", app()->http->getName()],
            ["is_deleted", "=", 0],
        ]);
        return $model->order("id desc");
    }


    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {


        $table->attribute('border', false);
        $table->column("id", "ID")->width(80)->sortable();
        $table->column("user.username", "账户")->width(150);
        $table->column("name", "员工姓名")->width(150);
        $table->column("nickname", "员工昵称");
        $table->column("avatar", "头像")->image(40, 40);
        $table->column("phone", "手机");
        $table->column("email", "邮箱");
        $table->column("status", "启用状态")->switch(function ($value, $row) {

            $this->inactiveText("禁用")->activeText("启用")->width(55);

            $userId =  app()->auth->getAdminId();
            // 自己跟超级管理员禁用
            if ($row->identity->is_super_admin == 1  || $userId == $row['id']) {
                $this->disabled();
            }
        })->width(90);


        return $table;
    }


    /**
     * @param Form $form
     * @param Request $request
     * @return bool|Form
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function form(Form $form, Request $request)
    {
        $authList = $this->app->db->name('SystemAuth')
            ->field("id,pid,name as title")->where([
                'status' => 1,
                'plugin_name' => app()->http->getName(),
            ])->select();
        $authList = TreeArray::arr2tree($authList->toArray(), 'id', 'pid', 'children');

        $form->labelWidth(80);
        $form->text('username', "账号")->resolveUsing(function ($value, $row) {
            if (isset($row['id'])) {
                $this->disabled();
                return $row->user->username;
            }
            return '';
        })->rules("require");
        $form->text('name', "员工姓名")->rules("require");
//            ->creationRules('unique:SystemAdmin')

        $form->text('nickname', "用户昵称")->rules("require");
        $form->text('phone', "手机")->rules('mobile');
        $form->text('email', "email")->rules("email");
        $form->image('avatar', "头像");
        $form->upload('tests', "上传")->file()->default('fdfd');
        $form->tree('auth_set', "角色")->options($authList)->fillUsing(function ($data, $model) {
            return $model->auth_set = implode(',', $data['auth_set']);
        })->resolveUsing(function ($value) {
            if (is_string($value)) {
                return explode(',', $value);
            }
        })->expandAll()->indent(28)->minWidth(90)->props("nodeKey", "id");

        return $form;
    }


    /**
     * 注册行操作
     * @return array|mixed
     */
    protected function actions()
    {
        return [
            EditUserPasswordAction::make('设置密码')->canRun(function ($request, $model) {

                if ($model['plugin_name'] != app()->http->getName()) {
                    return false;
                }
                $userId =  app()->auth->getAdminId();
//                超级管理员只能自己设置密码
                if ($model->identity->is_super_admin == 1 &&  $userId != $model['id']) {
                    return false;
                }
                return true;
            })
        ];
    }


    /**
     * 注册批量操作
     * @return array
     */
    protected function batchActions()
    {
        return [];
    }


    /**
     * @param $action
     * @param $request
     * @return mixed
     */
    protected function deleteAction($action, $request)
    {
        return $action->canRun(function ($request, $model) {
            if ($model['plugin_name'] != app()->http->getName()) {
                return false;
            }

            //超级管理员不能删除
            if ($model->identity->is_super_admin == 1) {
                return false;
            }
            return true;
        });
    }

    /**
     * @param \quick\actions\RowAction|\quick\admin\actions\Action $action
     * @param Request $request
     * @return \quick\actions\RowAction|\quick\admin\actions\Action
     */
    protected function editAction($action, $request)
    {
        return $action->canRun(function ($request, $model) {
            if ($model['plugin_name'] != app()->http->getName()) {
                return false;
            }
            $userId =  app()->auth->getAdminId();
            if ($model->identity->is_super_admin == 1 && $userId != $model['id']) {
                return false;
            }

            return true;
        });
    }


    /**
     * @param \quick\admin\actions\Action $action
     * @param Request $request
     * @return \quick\admin\actions\Action
     */
    protected function addAction($action, $request)
    {
        return $action->beforeSaveUsing(function ($data, $request) {

            $data['plugin_name'] = app()->http->getName();
            $user = SystemUser::createAdminUser($data['username']);
            $data['user_id'] = $user->id;

            return $data;
        });
    }


}
