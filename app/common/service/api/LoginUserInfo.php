<?php


namespace app\common\service\api;


use app\common\service\CommonService;

class LoginUserInfo extends CommonService
{

    public $nickname;
    public $username;
    public $avatar;

    /** @var string 当前平台  */
    public $platform;
    public $platform_openid;

    /**
     * @var string $scope
     * auth_info 用户授权
     * auth_base 静默授权
     */
    public $scope = 'auth_info';
    public $unionId = '';

    /** @var string  */
    public $password = '';

    /** @var string 用户归属平台  */
    public $user_platform;
    public $user_platform_openid;
    public $subscribe = 0;
    public $phone = '';


    public function rules(): array
    {
        return [
            'username' => 'require',
            'platform' => 'require',
            'platform_openid' => 'require',
            'avatar' => "max:150",
        ];
    }

    public function message(): array
    {
        return [

        ];
    }

}