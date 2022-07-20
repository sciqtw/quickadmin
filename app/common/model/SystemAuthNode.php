<?php
declare (strict_types = 1);

namespace app\common\model;


use think\Model;
use think\model\Pivot;


/**
 * Class SystemAuth
 *
 * @property int $auth 角色
 * @property int $id
 * @property int $node_id
 * @property string $node 节点
 * @package app\model
 */
class SystemAuthNode extends Pivot
{
    protected $name = 'system_auth_node';

}
