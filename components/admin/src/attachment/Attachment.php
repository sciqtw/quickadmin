<?php
declare(strict_types=1);

namespace components\admin\src\attachment;


use app\common\model\SystemAttachmentStorage;
use app\common\service\admin\AttachmentCateEditService;
use app\common\service\admin\AttachmentCateListService;
use app\common\service\admin\AttachmentEditService;
use app\common\service\admin\AttachmentListService;
use app\common\service\admin\AttachmentMoveService;
use app\common\service\admin\AttachmentUpdateService;
use components\admin\src\actions\attachment\AttachmentCateListAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\Resource;
use think\Request;

/**
 * 素材管理
 * @AdminAuth(title="素材管理",auth=true,login=true,menu=true)
 * @package app\admin\resource\auth
 */
class Attachment extends Resource
{
    /**
     * @var string
     */
    protected $title = '素材管理';

    /**
     * @var string
     */
    protected $description = "素材管理";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemGroup";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["title"];


    private function getStorageId()
    {

        if(app()->auth->isLogin()){
            $user_id = app()->auth->user->id;

            $pluginName = config('quick.auth.scope_key','admin');
            $map1 = [
                ['plugin_name', '=', $pluginName],
                ['user_id', '=', $user_id],
                ['is_deleted', '=',0],
            ];
            $map2 = [
                ['plugin_name', '=', $pluginName],
                ['user_id', '=', 0],
                ['is_deleted', '=',0],
            ];
            $storage = SystemAttachmentStorage::whereOr([$map1,$map2])->find();
            if(!$storage){
                $storage = SystemAttachmentStorage::create(['user_id' => 0,'plugin_name' => $pluginName,'is_deleted' => 0]);
            }
            return $storage->id;
        }

        return 0;
    }

    /**
     * 添加编辑分类
     * @AdminAuth(title="编辑分类",auth=true,menu=true,login=true)
     * @param AttachmentCateEditService $service
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function editCate(AttachmentCateEditService $service)
    {
        $service->attributes = $this->request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->save());
    }

    /**
     * 分类列表
     * @AdminAuth(title="分类列表",auth=true,menu=true,login=true)
     * @param AttachmentCateListService $service
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cateList(AttachmentCateListService $service)
    {
        $service->attributes = $this->request->get();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->list());
    }


    /**
     * 删除分类
     * @AdminAuth(title="删除分类",auth=true,menu=true,login=true)
     * @param AttachmentCateEditService $service
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delCate(AttachmentCateEditService $service)
    {
        $service->attributes = $this->request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->del());
    }


    /**
     * 素材列表
     * @AdminAuth(title="素材列表",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentListService $service
     * @return \think\response\Json
     */
    public function list(Request $request, AttachmentListService $service)
    {
        $service->attributes = $request->get();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->search());
    }


    /**
     * 删除图片
     * @AdminAuth(title="删除图片",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentEditService $service
     * @return \think\response\Json
     */
    public function delete(Request $request, AttachmentEditService $service)
    {
        $service->attributes = $request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->del());
    }


    /**
     * 修改图片名称
     * @AdminAuth(title="修改图片名称",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentUpdateService $service
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function update(Request $request, AttachmentUpdateService $service)
    {
        $service->attributes = $request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->save());
    }


    /**
     * 放入回收站
     * @AdminAuth(title="放入回收站",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentEditService $service
     * @return \think\response\Json
     */
    public function recycle(Request $request, AttachmentEditService $service)
    {
        $service->attributes = $request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->recycle());
    }


    /**
     * 迁移
     * @AdminAuth(title="迁移",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentMoveService $service
     * @return \think\response\Json
     */
    public function moveCate(Request $request, AttachmentMoveService $service)
    {
        $service->attributes = $request->post();
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->move());
    }


    /**
     * 上传
     * @AdminAuth(title="上传",auth=true,menu=true,login=true)
     * @param Request $request
     * @param AttachmentListService $service
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function upload(Request $request, AttachmentListService $service)
    {
        $service->cate_id = $request->post('cate_id');
        $service->type = $request->post('type','1');
        $service->file = $request->file("file");
        $service->storage_id = $this->getStorageId();
        return $this->responseJson($service->save());
    }

    /**
     * @inheritDoc
     */
    protected function actions()
    {

        return [
            new AttachmentCateListAction()
        ];
    }

    /**
     * @inheritDoc
     */
    protected function batchActions()
    {
        // TODO: Implement batchActions() method.
        return [];
    }

    /**
     * @AdminAuth(title="responseJson",auth=false,menu=false,login=false)
     * @param $data
     * @return \think\response\Json
     */
    protected function responseJson($data)
    {
        if(!isset($data['code'])){
            $data = [
                'code' => 0,
                'msg' => '',
                'data' => $data,
            ];
        }
        return json($data);
    }
}
