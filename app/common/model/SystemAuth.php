<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;


/**
 * Class SystemAuth
 *
 * @property bool $status 状态(0:禁用,1:启用)
 * @property int $id
 * @property int $level 管理级别
 * @property int $pid 父id
 * @property string $created_at 创建时间
 * @property string $desc 说明
 * @property string $name 角色名称
 * @property string $node_set 权限节点集合 多个值,号隔开
 * @property string $plugin_name 系统插件plugin_name
 * @property string $updated_at 更新时间
 * @property-read \app\common\model\SystemNode[] $nodes
 * @package app\model
 */
class SystemAuth extends BaseModel
{
    protected $name = 'system_auth';

    public function nodes()
    {
        return $this->belongsToMany(SystemNode::class,SystemAuthNode::class,"node_id","auth");
    }


}
