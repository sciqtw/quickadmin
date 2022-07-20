<?php
declare (strict_types = 1);

namespace app\common\model;


/**
 * Class app\common\model\UserIdentity
 *
 * @property bool $is_admin 是否为管理员
 * @property bool $is_delete 1 已删除
 * @property bool $is_deleted 1 已删除
 * @property bool $is_operator 是否为操作员|员工
 * @property bool $is_super_admin 是否为超级管理员
 * @property int $id 用户身份表
 * @property int $member_level 会员等级:0.普通成员
 * @property int $user_id
 */
class SystemUserIdentity extends BaseModel
{

    protected $name = 'system_user_identity';

    
}