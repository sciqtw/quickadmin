<?php
declare (strict_types=1);

namespace app\common\service\admin;

use app\common\model\SystemAttachment;
use app\common\service\CommonService;
use quick\admin\library\service\UploadService;

/**
 * Class AttachmentListService
 * @package plugins\mall\service\admin
 */
class AttachmentListService extends CommonService
{


    /** @var integer 仓储ID */
    public $page;
    public $pageSize;

    /** @var integer 类型 */
    public $type;

    /** @var boolean 加入回收站 */
    public $is_recycle;

    /** @var boolean 删除 */
    public $is_deleted;
    public $cate_id;
    public $storage_id;
    public $file;
    public $keyword;


    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'page' => 'require|integer|default:1',
            'pageSize' => 'integer|default:20',
            'storage_id' => 'integer|default:0',
            'cate_id' => 'default:0',
            'keyword' => 'max:100',
            'type' => 'require|integer',
            'is_recycle' => 'require',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'page' => '页码',
            'type' => '类型',
            'is_recycle' => '加入回收站',
            'is_deleted' => '删除',
        ];
    }


    public function search()
    {
        if (!$this->validate()) {
            return $this->error($this->getFirstError());
        }


        $model = SystemAttachment::where( [
            'type' => $this->type,
            'storage_id' => $this->storage_id,
            'is_recycle' => $this->is_recycle,
            'is_deleted' => 0
        ]);
        if ($this->keyword !== '') {
            $model->whereLike('name', "%{$this->keyword}%");
        }

        if($this->cate_id > 0){
            $model->where('attachment_cate_id',  $this->cate_id );
        }


        $res = $model->order('id desc')->paginate($this->pageSize);

        $data = [
            'list' => $res->items(),
            'pagination' => [
                'pageSize' => (int)$this->pageSize,
                'totalCount' => $res->total(),
            ],
        ];
        return $this->success('success', $data);
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

        try {


            $update = UploadService::instance();
            $update->setFile($this->file);
            $res = $update->save();
            if ($res && $res['uploaded']) {
                $model = new SystemAttachment();
                $res = $model->save([
                    'storage_id' => $this->storage_id,
                    'attachment_cate_id' => $this->cate_id > 0 ? $this->cate_id : 0,
                    'name' => $res['name'],
                    'size' => $res['size'],
                    'image' => $res['url'],
                    'thumb_image' => $res['url'],
                    'type' => 1,
                    'is_recycle' => 0,
                    'is_deleted' => 0,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'created_at' => date("Y-m-d H:i:s"),
                ]);
                if($res){
                    return $this->success('success', $model->toArray());
                }

            }
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

        return $this->error('上传失败');
    }

}
