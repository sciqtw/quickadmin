<?php
declare (strict_types=1);

namespace app\common\model;

use quick\admin\library\tools\CodeTools;
use think\Model;


/**
 * Class SystemUser
 *
 * @property bool $is_deleted 删除: 1已删除 0未删除
 * @property bool $login_fail_num 失败次数
 * @property bool $status 状态 1:启用, 0:禁用
 * @property int $id 账号id
 * @property int $login_num 登录次数
 * @property string $avatar 头像
 * @property string $create_ip_at 创建ip
 * @property string $created_at
 * @property string $deleted_at
 * @property string $email 电子邮箱
 * @property string $last_login_ip_at 最后一次登录ip
 * @property string $login_at
 * @property string $nickname 昵称
 * @property string $password 密码
 * @property string $phone 手机号
 * @property string $salt 密码盐
 * @property string $updated_at
 * @property string $username 账户名称
 * @property-read \app\common\model\SystemUserIdentity $identity
 * @property-read \app\common\model\SystemUserInfo $user_info
 * @package app\common\model
 */
class SystemUser extends BaseModel
{

    /** @var string h5平台标识 */
    const PLATFORM_H5 = 'mobile';

    /** @var string 微信平台公众号 */
    const PLATFORM_WECHAT = 'wechat';

    /** @var string 微信平台小程序 */
    const PLATFORM_WXAPP = 'wxapp';

    /** @var string 注册事件 */
    const  EVENT_REGISTER = "event_register";

    /** @var string 登录事件 */
    const  EVENT_LOGIN = "event_login";


    protected $name = 'system_user';


    /**
     * @param String $password
     * @param String $salt
     * @return String
     */
    public static function hashPassword(string $password, string $salt = ''): string
    {
        return md5(md5($password) . $salt);
    }


    /**
     * 账户身份
     * @return \think\model\relation\HasOne
     */
    public function identity()
    {
        return $this->hasOne(SystemUserIdentity::class, "user_id");//->where("is_delete",0);
    }


    /**
     * 用户详细信息
     *
     * @return \think\model\relation\HasOne
     */
    public function userInfo()
    {
        return $this->hasOne(SystemUserInfo::class, "user_id");
    }


    /**
     * @param string $username
     * @param array $data
     * @param array $admin
     * @param int $is_super_admin
     * @param int $type 1 超级管理员 2 员工
     * @return SystemUser|array|mixed|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function createAdminUser(string $username, array $data = [], array $admin = [], int $type = 0)
    {
        $user = SystemUser::withJoin(['identity'])->where([
            'system_user.username' =>  $username,
            'system_user.is_deleted' => 0,
        ])->where(function ($query){
            $query->whereOr([
                'identity.is_admin' => 1,
                'identity.is_super_admin' => 1,
            ]);
        })->find();

        if ($user) {
            throw new \Exception('账户名已被占用');
        }

        $user = new SystemUser();
        $user->username = $username;
        $user->nickname = $data['nickname'] ?? $username;
        $user->status = 1;

        $password = $data['password'] ?? CodeTools::random(23,3);
        $salt = $data['salt'] ?? CodeTools::random(4,3);
        unset($data['username']);
        unset($data['password']);
        unset($data['salt']);

        $user->password = static::hashPassword($password, $salt);
        $user->salt = $salt;

        $res = $user->save($data);

        if (!$res) {
            throw new \Exception('添加失败');
        }

        $identity = new SystemUserIdentity();
        $identity->is_admin = 1;
        $identity->is_super_admin = $type == 1 ? 1 : 0;
        $identity->is_operator = $type == 2 ? 1 : 0;
        $identity->user_id = $user->id;
        $res1 = $identity->save();
        if (!$res1) {
            throw new \Exception('添加失败');
        }
        if (!empty($admin) && $admin['plugin_name']) {
            $adminInfo = new SystemAdminInfo();
            $res = $adminInfo->save(array_merge(['name' => $username, 'user_id' => $user->id], $admin));
            if (!$res) {
                throw new \Exception('添加失败');
            }
        }


        return $user;

    }


}
