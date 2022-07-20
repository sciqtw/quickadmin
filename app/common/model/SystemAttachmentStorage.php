<?php
declare (strict_types = 1);

namespace app\common\model;

use quick\admin\http\model\Model;

/**
 * Class SystemAttachmentStorage
 *
 * @property integer  $id              
 * @property string   $plugin_name     插件标识
 * @property integer  $user_id         
 * @property boolean  $is_deleted      删除:0=未删除,1=已删除
 * @property string   $deleted_at      删除日期
 * @property string   $created_at      创建日期
 * @property string   $updated_at      更新日期
 *
 * @package app\common\model
 */
class SystemAttachmentStorage extends Model
{


    /**
     * 验证规则
     * @return array
     */
    protected function rules(): array
    {
        return [
            'id' => 'integer',
            'plugin_name' => 'require|max:128',
            'user_id' => 'require|integer',
        ];
    }

  
    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [
            'id' => 'id',
            'plugin_name' => '插件标识',
            'user_id' => 'user_id',
            'is_deleted' => '删除',
            'deleted_at' => '删除日期',
            'created_at' => '创建日期',
            'updated_at' => '更新日期',
        ];
    }


    /**
     * 删除
     */
    public static function getIsDeletedList():array
    {
        return [
            0 => __('Is_deleted 0'),
            1 => __('Is_deleted 1'),
        ];
    }
    

  
}
