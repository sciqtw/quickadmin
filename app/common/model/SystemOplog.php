<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;


/**
 * Class SystemOplog
 *
 * @property int $id
 * @property string $action 操作行为名称
 * @property string $content 操作内容描述
 * @property string $create_at 创建时间
 * @property string $geoip 操作者IP地址
 * @property string $node 当前操作节点
 * @property string $username 操作人用户名
 * @package app\model
 */
class SystemOplog extends BaseModel
{
    protected $name = 'system_oplog';




}
