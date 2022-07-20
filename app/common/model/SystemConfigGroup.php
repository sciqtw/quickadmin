<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;

/**
 * Class SystemConfigGroup
 *
 * @property integer  $id              ID
 * @property integer  $parent_id       父级ID
 * @property string   $group           分组变量名称
 * @property string   $title           分组别名
 * @property boolean  $status          状态:1=启用,0=禁用
 * @property boolean  $show            显示:1=显示,0=隐藏
 * @property integer  $type            分组类型
 * @property integer  $sort            排序
 *
 * @package app\common\model
 */
class SystemConfigGroup extends BaseModel
{


    /**
     * 验证规则
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'integer',
            'parent_id' => 'require|integer',
            'group' => 'require|max:50',
            'title' => 'require|max:255',
            'status' => 'integer',
            'show' => 'integer',
            'type' => 'integer',
            'sort' => 'integer',
        ];
    }


    /**
     * @return array
     */
    public function attrLabels(): array
    {
        return [
            'id' => 'ID',
            'parent_id' => '父级ID',
            'group' => '分组变量名称',
            'title' => '分组别名',
            'status' => '状态',
            'show' => '显示',
            'type' => '分组类型',
            'sort' => '排序',
        ];
    }


    /**
     * 状态
     */
    public static function getStatusList():array
    {
        return [
            1 => __('Status 1'),
            0 => __('Status 0'),
        ];
    }


    /**
     * 显示
     */
    public static function getShowList():array
    {
        return [
            1 => __('Show 1'),
            0 => __('Show 0'),
        ];
    }



}
