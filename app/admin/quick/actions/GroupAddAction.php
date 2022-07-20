<?php

namespace app\admin\quick\actions;


use app\common\service\common\BuildGroupViewService;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;
use quick\admin\http\response\JsonResponse;
use think\facade\Db;
use think\facade\Route;

/**
 * 添加数据组
 * @AdminAuth(auth=true,menu=true,login=true,title="添加数据组")
 * @package app\admin\resource\example\actions
 */
class GroupAddAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemGroup";


    protected function initAction()
    {
        $this->getDisplay()->type('primary')
            ->props('plain',true)
            ->size('small');
        $this->dialog();
    }



    public function form()
    {
        $form = BuildGroupViewService::editGroupForm();
        return $form;
    }


    /**
     * 动作异步数据接口
     *
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     */
    public function load()
    {

        if (!$this->handleCanRun($this->request, [])) {
            quick_abort(500, '你无权访问');
        }
        $form = $this->form();
        $form->resolve([]);
        $form->url($this->storeUrl());
        $form->style("background-color", '#FFFFFF');
        $form = $this->resolveComponent($form);
        return $this->response()->success("success", $form);
    }


    /**
     * 动作提交数据接口
     *
     * @return mixed|JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\Exception
     */
    public function store()
    {
        $model = $this->getModel();

        if (!$this->handleCanRun($this->request, $model)) {
            quick_abort(500, '你无权访问');
        }

        $form = $this->form();
        $data = (array)$form->getSubmitData($this->request, 1);
        Db::startTrans();
        try {
            if ($this->beforeSavingCallback instanceof \Closure) {
                $beforeSavingCallback = \Closure::bind($this->beforeSavingCallback,$this);
                $data = call_user_func($beforeSavingCallback, $data, $this->request);
            }
            $module = Route::buildUrl()->getAppName();
            $data['plugin'] = $module;
            $res = $model->create($data);
            if (!$res->id) {
                throw new \Exception("添加失败".$res->getError());
            }
            if ($this->afterSavingCallback instanceof \Closure) {
                $afterSavingCallback = \Closure::bind($this->afterSavingCallback,$this);
                $res = call_user_func($afterSavingCallback, $res, $this->request);
                if ($res === false) {
                    throw new \Exception("添加失败");
                }
            }

            if ($res instanceof JsonResponse) {
                $response = $res;
            } else {
                $response = $this->response()->success("添加成功")->event('refresh',[],0,true);
            }

            if ($this->isPage()) {
                $response->push($this->backUrl("index"));
            }
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
            $response = $this->response()->error($e->getMessage());
        }


        return $response;
    }


}
