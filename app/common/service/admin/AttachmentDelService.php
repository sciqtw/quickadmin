<?php
declare (strict_types = 1);

namespace app\common\service\admin;

use app\common\model\SystemAttachment;
use app\common\service\CommonService;
use think\facade\Db;

/**
 * Class AttachmentDelService
 * @package plugins\mall\service\admin
 */
class AttachmentDelService extends CommonService
{


    /** @var integer  */
    public $ids;

    public $storage_id;



    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => 'require|array',
            'storage_id' => 'require|integer',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'ids' => '图片'
        ];
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function del()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        $res = SystemAttachment::update([
            'is_deleted' => 1
        ],['id' => $this->ids,'storage_id' =>$this->storage_id ??0]);


        if($res){
            return $this->success('删除成功');
        }

        return $this->error('删除失败');
    }


}
