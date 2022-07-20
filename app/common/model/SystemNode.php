<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;


/**
 * Class SystemNode
 *
 * @property bool $is_auth 是否启动RBAC权限控制
 * @property bool $is_login 是否启动登录控制
 * @property bool $is_menu 是否可设置为菜单
 * @property bool $level 节点层级
 * @property bool $status 状态(0:禁用,1:启用)
 * @property int $id
 * @property int $sort 排序
 * @property string $condition 条件
 * @property string $created_at 创建时间
 * @property string $mode controller  resource
 * @property string $node 节点规则
 * @property string $plugin_name 系统插件plugin_name
 * @property string $pnode 父节点
 * @property string $remark 备注
 * @property string $title 规则名称
 * @property string $updated_at 更新时间
 * @package app\model
 */
class SystemNode extends BaseModel
{
    protected $name = 'system_node';

}
