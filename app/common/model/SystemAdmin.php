<?php
declare (strict_types=1);

namespace app\common\model;


/**
 * Class app\common\model\AdminInfo
 *
 * @property bool $login_fail_num 失败次数
 * @property bool $status 状态 1：启用 0：禁用
 * @property int $id 员工id
 * @property int $user_id 账号id
 * @property string $auth_set 权限集
 * @property string $avatar 员工头像
 * @property string $created_at
 * @property string $deleted_at
 * @property string $email 员工邮箱
 * @property string $gender 员工性别
 * @property string $login_at
 * @property string $login_ip 最后一次登录ip
 * @property string $name 员工姓名
 * @property string $nickname 员工昵称
 * @property string $password 密码
 * @property string $phone 员工手机号
 * @property string $plugin_name 模块插件
 * @property string $salt 密码盐
 * @property string $updated_at
 * @property string $username 账号
 */
class SystemAdmin extends BaseModel
{

    protected $name = 'system_admin';


    /**
     * @param int $adminId
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getNodesByAdminId(int $adminId): array
    {
        $admin = self::where(['id' => $adminId])->find();
        if (!$admin) {
            return [];
        }
        if ($auth_ids = explode(",", $admin['auth_set'])) {
            $authIds = SystemAuth::where("id", "in", $auth_ids)->where('status', 1)->column("id");
            if (!empty($authIds)) {
                return SystemAuthNode::where("auth", "in", $authIds)->column("node");
            }
        }
        return [];
    }

    /**
     * @param String $password
     * @param String $salt
     * @return String
     */
    public static function hashPassword(string $password, string $salt = ''): String
    {
        return md5(md5($password) . $salt);
    }
}