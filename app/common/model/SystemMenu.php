<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;

/**
 * Class app\common\model\SystemMenu
 *
 * @property bool $is_admin 平台菜单 1
 * @property bool $status 状态(0:禁用,1:启用)
 * @property int $id
 * @property int $level 管理级别
 * @property int $pid 上级ID
 * @property int $sort 排序权重
 * @property string $badge badge
 * @property string $created_at 创建时间
 * @property string $icon 菜单图标
 * @property string $node 节点代码
 * @property string $params 链接参数
 * @property string $path 链接节点
 * @property string $plugin_name 系统插件plugin_name
 * @property string $target 打开方式 _blank _self
 * @property string $title 菜单名称
 * @property string $updated_at 更新时间
 */
class SystemMenu extends BaseModel
{

    protected $name = 'system_menu';


}
