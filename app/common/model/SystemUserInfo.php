<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;


/**
 * Class SystemUserInfo
 *
 * @property bool $is_deleted
 * @property float $balance 余额
 * @property float $total_balance 总余额
 * @property int $id 自增id
 * @property int $integral 积分
 * @property int $parent_id 上级id
 * @property int $temp_parent_id 临时上级
 * @property int $total_integral 最高积分
 * @property int $user_id 账号id
 * @property string $contact_way 联系方式
 * @property string $gender 性别
 * @property string $platform 用户所属平台标识 facebook,google,wechat,qq,weibo,twitter,weapp
 * @property string $platform_openid 平台id 如微信 openid
 * @property string $remark 备注
 * @package app\common\model
 */
class SystemUserInfo extends BaseModel
{

    protected $name = 'system_user_info';


    /**
     * @param $password
     * @param string $salt
     * @return string
     */
    public static function md5PassWord($password,$salt = 'mds')
    {
        return md5($password.$salt);
    }

}
