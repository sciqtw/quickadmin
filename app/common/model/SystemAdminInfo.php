<?php
declare (strict_types = 1);

namespace app\common\model;

use quick\admin\http\model\Model;

/**
 * Class SystemAdminInfo
 *
 * @property integer  $id              员工id
 * @property integer  $user_id         账号id
 * @property string   $plugin_name     模块标识
 * @property string   $auth_set        权限
 * @property string   $email           员工邮箱
 * @property string   $phone           员工手机号
 * @property string   $name            员工姓名
 * @property string   $nickname        员工昵称
 * @property string   $avatar          员工头像
 * @property string   $gender          员工性别
 * @property boolean  $status          状态:1启用,0:禁用
 * @property boolean  $is_deleted      是否删除:0=未删除,1=删除
 * @property string   $created_at      创建日期
 * @property string   $updated_at      更新日期
 *
 * @package app\common\model
 */
class SystemAdminInfo extends Model
{


    /**
     * 验证规则
     * @return array
     */
    protected function rules(): array
    {
        return [
            'id' => 'integer',
            'user_id' => 'require|integer',
//            'auth_set' => 'require',
        ];
    }


    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [
            'id' => '员工id',
            'user_id' => '账号id',
            'auth_set' => '权限',
            'is_deleted' => '是否删除',
            'created_at' => '创建日期',
            'updated_at' => '更新日期',
        ];
    }


    /**
     * 是否删除
     */
    public static function getIsDeletedList():array
    {
        return [
            0 => __('Is_deleted 0'),
            1 => __('Is_deleted 1'),
        ];
    }


    public function user()
    {
        return $this->belongsTo(SystemUser::class,'user_id','id');
    }


    /**
     * 账户身份
     * @return \think\model\relation\HasOne
     */
    public function identity()
    {
        return $this->hasOne(SystemUserIdentity::class, "user_id","user_id");
    }


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


}
