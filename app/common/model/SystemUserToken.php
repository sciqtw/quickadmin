<?php
declare (strict_types = 1);

namespace app\common\model;


use quick\admin\library\tools\CodeTools;
use think\Model;


/**
 * Class SystemUserToken
 *
 * @property int $expire_time 过期时间
 * @property int $user_id 会员ID
 * @property string $create_time 创建时间
 * @property string $token Token
 * @package app\model
 */
class SystemUserToken extends BaseModel
{
    protected $name = 'system_user_token';


    public static function createToken($user_id)
    {

        $token = CodeTools::random(32,3);
        //todo 处理多个token问题

        self::create([
            'token' => $token,
            'user_id' => $user_id,
            'create_time' => time(),
            'expire_time' => time() + 24*60*60*10,
        ]);

        return $token;
    }

}
