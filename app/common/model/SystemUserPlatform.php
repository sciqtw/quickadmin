<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;


/**
 * Class SystemUserPlatform
 *
 * @property bool $subscribe 微信是否关注
 * @property int $id 自增id
 * @property int $user_id 账号id
 * @property string $avatar 头像
 * @property string $created_at
 * @property string $gender 性别
 * @property string $nickname 昵称
 * @property string $password h5密码
 * @property string $platform 用户所属平台标识 facebook,google,wechat,qq,weibo,twitter,weapp
 * @property string $platform_openid 平台id 如微信 openid
 * @property string $unionid 微信unionid
 * @property string $updated_at
 * @package app\common\model
 */
class SystemUserPlatform extends BaseModel
{


    /**
     * @return \think\model\relation\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(SystemUser::class,'user_id');
    }


}
