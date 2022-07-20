<?php
declare (strict_types = 1);

namespace app\common\service\admin;

use app\common\model\SystemAttachmentCate;
use app\common\service\CommonService;
use think\facade\Db;

/**
 * Class AttachmentCateEditService
 * @package plugins\mall\service\admin
 */
class AttachmentCateEditService extends CommonService
{


    /** @var integer  */
    public $id;

    /** @var integer 父级ID */
    public $parent_id;

    /** @var string 名称 */
    public $name;

    public $storage_id;





    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'integer',
            'parent_id' => 'require|integer',
            'storage_id' => 'require|integer',
            'name' => 'require|max:64',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'id' => 'id',
            'parent_id' => '父级ID',
            'name' => '名称',
            'storage_id' => '仓储',
            'is_recycle' => '加入回收站',
            'is_deleted' => '删除',
        ];
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function save()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        if($this->id){
            $model = SystemAttachmentCate::where("id",$this->id)->find();
            if(!$model){
                return $this->error('数据异常');
            }
        }else{
            $model = new SystemAttachmentCate();
            $model->create_at = date("Y-m-d H:i:s");
            $model->parent_id = $this->parent_id;
            $model->storage_id = $this->storage_id;
            $model->is_recycle = 0;
            $model->is_deleted = 0;
        }

        $model->name = $this->name;
        $model->update_at = date("Y-m-d H:i:s");
        $res = $model->save();

        if($res){
            return $this->success('保存成功');
        }

        return $this->error('保存失败');
    }

    public function del()
    {
        if(empty($this->id)){
            return $this->error('提交参数有误！');
        }

        $model = SystemAttachmentCate::where("id",$this->id)->find();
        if(!$model){
            return $this->error('数据异常');
        }
        $model->is_deleted = 1;
        $model->save();
        return $this->success('删除成功');


    }

}
