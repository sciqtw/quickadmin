<?php
declare (strict_types = 1);

namespace app\common\service\admin;

use app\common\model\SystemAttachment;
use app\common\service\CommonService;
use quick\admin\library\service\UploadService;
use think\facade\Db;

/**
 * Class AttachmentEditService
 * @package plugins\mall\service\admin
 */
class AttachmentEditService extends CommonService
{


    /** @var array ids */
    public $ids;
    public $storage_id;
    public $cate_id;




    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'ids' => 'require|array',
            'storage_id' => 'require|integer',
            'cate_id' => 'integer',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'ids' => '图片',
        ];
    }

    public function checkScene(): array
    {
        return [

        ];
    }

    public function del()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        $list = Db::name('system_attachment')->where('storage_id' ,$this->storage_id ?? 0)->where("id","in",$this->ids)->save([
            'is_deleted' => 1,
        ]);

        return $this->success('success',$list);
    }

    public function recycle()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }

        $res = Db::name('system_attachment')->where('storage_id' ,$this->storage_id ?? 0)->where("id","in",$this->ids)->save([
            'is_recycle' => 1,
        ]);

        return $this->success('success',$res);
    }

    /**
     * @return array
     * @throws \think\Exception
     */
    public function save()
    {
        if (!$this->file) {
            return $this->error('文件不能为空');
        }

        $update = UploadService::instance();
        $update->setFile(request()->file("file"));
        $res  = $update->save();
        if($res && $res['uploaded']){
            $model = new SystemAttachment();
            $model->save([
                'storage_id' => $this->storage_id ?? 0,
                'attachment_cate_id' => $this->cate_id,
                'name' => $res['name'],
                'size' => '',
                'image' => $res['url'],
                'thumb_image' => $res['url'],
                'type' => 1,
                'is_recycle' => 0,
                'is_deleted' => 0,
            ]);
            return $this->success('success',$model->toArray());
        }
        return $this->error('上传失败');
    }

}
