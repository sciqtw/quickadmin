<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;


/**
 * Class SystemGroup
 *
 * @property bool $status 状态1：开启；0：关闭
 * @property int $group_id 数据组id
 * @property int $id ID
 * @property int $sort 数据排序
 * @property string $created_at
 * @property string $plugin_name 模块插件
 * @property string $updated_at
 * @property string $value 数据组数据（json数据）
 * @package app\model
 */
class SystemGroupData extends BaseModel
{
    protected $name = 'system_group_data';

    public function group()
    {
        return $this->belongsTo(SystemGroup::class,"group_id","id");
    }
}
