<?php

namespace app\admin\quick\actions;


use quick\admin\actions\Action;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;
use quick\admin\library\cloud\CloudException;
use quick\admin\library\cloud\CloudNoLoginException;
use quick\admin\library\cloud\CloudService;
use quick\admin\library\service\PluginService;
use quick\admin\library\service\QueueService;
use quick\admin\library\tools\HttpTools;
use think\Request;

/**
 * 插件会员信息
 * @AdminAuth(title="插件会员信息",auth=true,menu=true,login=true)
 * @package app\admin\resource\example\actions
 */
class PluginUserInfoAction extends Action
{

    /**
     * 模型主键
     *
     * @var string
     */
    public static $pk = "name";

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = 'app\common\model\SystemPlugin';


    protected function initAction()
    {

        try {
            $res = CloudService::instance()->userInfo();
            $this->name = "会员：".$res['data']['username'];
        }catch (\Exception $e){
            $this->name = '会员信息';
        }

        $this->display(Component::button($this->name));
        $this->dialog();
//        $this->confirm('确定删除吗？');
    }


    public function load()
    {


        try {

            $res = CloudService::instance()->userInfo();
            $form = Form::make();
            $form->text('username', '用户名称')->disabled();
            $form->resolve($res['data']);
            $form->url($this->storeUrl(['type' => 'logout']));
            $form->hideFooter();
            $form->footer('d', 'd')
                ->props('width','50px')
                ->hideReset()
//                ->hideCancel()
                ->submitBtn(Component::button("退出登录",'primary'));

        } catch (CloudNoLoginException $e) {
            $form = $this->form();
            $form->url($this->storeUrl(['type' => 'login']));
        } catch (\Exception $e) {
            return $this->response()->error($e->getMessage());
        }


        return $this->response()->success("success", $form);
    }

    protected function form()
    {
        $form = Form::make();
        $form->resolve([]);
//        <el-alert title="error alert" type="error" />
        $form->row(function (Row $row){
            $row->col(24,Component::custom('el-alert')->props([
                'title' => '使用QuickAdmin官方账户登录',
                'type' => 'error',
            ]))->style('margin-bottom','20px');
        });
        $form->text('mobile', '账户')->placeholder('您的手机、用户名或邮箱')->required();
        $form->text('password', '密码')->placeholder('您的密码')->password()->required();
        return $form;
    }


    public function store()
    {

        $type = $this->request->param('type','login');
        if($type == 'logout'){
            CloudService::instance()->logout();
            return  $this->response()->success('退出成功')->refresh();
        }

        try {
            $form = $this->form();
            $data = (array)$form->getSubmitData($this->request);

            $res = CloudService::instance()->login([
                'mobile' => $data['mobile'],
                'password' => $data['password'],
                'type' => 'username',
            ]);

            $response = $this->response()->success('success', $res)
                ->message('登录成功')->refresh();


        } catch (\Exception $exception) {
            $response = $this->response()->error($exception->getMessage());
        }
        return $response;
    }


}
