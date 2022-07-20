<?php
declare (strict_types = 1);

namespace app\common\service\admin;

use app\common\model\SystemAttachmentCate;
use app\common\service\CommonService;
use quick\admin\library\tools\TreeArray;
use think\facade\Db;

/**
 * Class AttachmentCateListService
 * @package plugins\mall\service\admin
 */
class AttachmentCateListService extends CommonService
{

    public $is_recycle;
    public $storage_id;

    public function rules(): array
    {
        return [
            'is_recycle' => 'integer',
            'storage_id' => 'integer'
        ];
    }

    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list()
    {

        $list = SystemAttachmentCate::field("id,parent_id,name as label")->where([
            'is_deleted' => 0,
            'storage_id' => $this->storage_id ??0,
            'is_recycle' => 0,
        ])->select()->toArray();

        $list = TreeArray::arr2tree($list, "id", "parent_id",'children');
        return $this->success('success',$list);
    }

}
