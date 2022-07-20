<?php
declare (strict_types = 1);

namespace app\common\service\admin;

use app\common\model\SystemAttachment;
use app\common\service\CommonService;

/**
 * Class AttachmentUpdateService
 * @package plugins\mall\service\admin
 */
class AttachmentUpdateService extends CommonService
{


    /** @var array $id */
    public $id;
    public $name;
    public $storage_id;




    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'require|integer',
            'name' => 'require|max:40',
            'storage_id' => 'integer|default:0',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'id' => '图片',
            'name' => '图片名称',
        ];
    }




    /**
     * @return array
     * @throws \think\Exception
     */
    public function save()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        $model = SystemAttachment::where([
            'is_deleted' => 0,
            'storage_id' => $this->storage_id,
            'id' => $this->id,
        ])->find();
        if(!$model){
            return $this->error('修改失败，图片不存在');
        }
        $model->name = $this->name;
        $res = $model->save();
        if($res){
            return $this->success('修改成功');
        }
        return $this->error('修改失败');
    }

}
