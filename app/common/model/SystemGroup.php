<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;


/**
 * Class SystemGroup
 *
 * @property int $id 组合数据ID
 * @property string $created_at
 * @property string $fields 数据组字段（json数据）
 * @property string $info 数据提示
 * @property string $name 数据字段名称
 * @property string $plugin_name 模块插件
 * @property string $title 数据组名称
 * @property string $updated_at
 * @package app\model
 */
class SystemGroup extends BaseModel
{
    protected $name = 'system_group';

}
