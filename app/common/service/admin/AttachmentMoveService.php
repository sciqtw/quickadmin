<?php
declare (strict_types=1);

namespace app\common\service\admin;

use app\common\model\SystemAttachment;
use app\common\service\CommonService;
use plugins\mall\service\BaseService;
use quick\admin\library\service\UploadService;
use think\facade\Db;

/**
 * Class AttachmentMoveService
 * @package plugins\mall\service\admin
 */
class AttachmentMoveService extends CommonService
{


    /** @var array ids */
    public $ids;

    /** @var int cate_id */
    public $cate_id;
    public $storage_id;


    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => 'require|array',
            'cate_id' => 'require|integer',
            'storage_id' => 'integer|default:0',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'ids' => '迁移图片',
            'cate_id' => '迁移分类',
        ];
    }


    public function move()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        $cate_id = $this->cate_id > 0 ? $this->cate_id : 0;
        $res = SystemAttachment::update([
            'attachment_cate_id' => (int)$cate_id,
        ], ['id' => $this->ids, 'storage_id' => $this->storage_id]);
        if (!$res) {
            return $this->error('迁移失败！');
        }

        return $this->success('迁移成功');
    }


}
