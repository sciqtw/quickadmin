<?php


namespace app\common\service\api;


use app\common\ApiCode;
use app\common\model\SystemUserIdentity;
use app\common\model\SystemUserInfo;
use app\common\service\CommonService;
use app\common\events\UserEvent;
use app\common\model\SystemUser;
use app\common\model\SystemUserPlatform;
use app\common\model\SystemUserToken;
use quick\admin\library\tools\CodeTools;
use think\facade\Log;

abstract class LoginService extends CommonService
{

    /**
     * 第三方平台登录数据
     *
     * @return LoginUserInfo
     */
    abstract protected function getPlatformLoginInfo(): LoginUserInfo;


    /**
     * 静默模式是否注册
     * @var bool
     */
    protected $auth_base_register = false;


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function login()
    {
        try {

            /** @var LoginUserInfo $loginUserInfo */
            $loginUserInfo = $this->getPlatformLoginInfo();
            $loginUserInfo->user_platform = $loginUserInfo->user_platform ?: $loginUserInfo->platform;
            $loginUserInfo->user_platform_openid = $loginUserInfo->user_platform_openid ?: $loginUserInfo->platform_openid;
            if(!$loginUserInfo->validate()){
                throw new \Exception($loginUserInfo->getFirstError());
            }

        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            return $this->responseData(ApiCode::CODE_ERROR,$e->getMessage(),[
                'field' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        }

        $user = $this->getUserByUserPlatform($loginUserInfo);

        $token = null;
        if($user){
            $token = SystemUserToken::createToken($user->id);
        }

        /** 静默模式 */
        if ($loginUserInfo->scope == 'auth_base' ) {
            if ($user) {
                $this->triggerEvent($user, false);
                return $this->success('success',['token' => $token]);
            }
            if(!$this->auth_base_register){
                // 静默模式不开启注册
                return $this->error('授权失败',['token' => $token]);
            }

        }



        $is_register = false;
        $this->startTrans();
        try {
            if (!$user) {
                $is_register = true;
                $user = new SystemUser();
                $user->username = $loginUserInfo->username;
                $loginUserInfo->phone && $user->phone = $loginUserInfo->phone ;
                $user->status = 1;
                $user->password = CodeTools::random(32,3);

            }
//            $user->unionid = $loginUserInfo->unionId;
            $user->nickname = $loginUserInfo->nickname;
            $user->avatar = $loginUserInfo->avatar;
            if (!$user->save()) {
                throw new \Exception('授权登录失败！');
            }

            // 设置插件应用会员信息
            $this->setPluginUserInfo($user,$loginUserInfo->platform_openid,$loginUserInfo->platform);

            // 用户角色表
            $userIdentity = SystemUserIdentity::where([
                'user_id' => $user->id,
                'is_deleted' => 0
            ])->find();
            if (!$userIdentity) {
                $userIdentity = new SystemUserIdentity();
                $userIdentity->user_id = $user->id;
            }
            if (!$userIdentity->save()) {
               throw new \Exception('授权登录失败！');
            }

            // 用户平台信息表
            $userPlatform = SystemUserPlatform::where([
                'user_id' => $user->id,
                'platform' => $loginUserInfo->user_platform,
            ])->find();
            if (!$userPlatform) {
                $userPlatform = new SystemUserPlatform();
                $userPlatform->user_id = $user->id;
                $userPlatform->platform = $loginUserInfo->user_platform;
            }
            $userPlatform->platform_openid = $loginUserInfo->user_platform_openid;
            $userPlatform->unionid = $loginUserInfo->unionId;
            $userPlatform->password = $loginUserInfo->password;
            $userPlatform->subscribe = $loginUserInfo->subscribe;
            if (!$userPlatform->save()) {
                throw new \Exception('授权登录失败！');
            }
            $this->commit();
            $token = SystemUserToken::createToken($user->id);
            $this->triggerEvent($user, $is_register);
        }catch (\Exception $e){
            $this->rollback();
            return $this->error($e->getMessage(),[
                'line' => $e->getFile(),
                'code' => $e->getCode(),
            ]);
        }

        return $this->success('success',['token' => $token]);
    }


    /**
     * 根据第三方表获取用户信息
     *
     * @param LoginUserInfo $userInfo
     * @return SystemUser|array|mixed|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserByUserPlatform(LoginUserInfo $userInfo)
    {

        $userPlatform = SystemUserPlatform::where([
            'platform_openid' => $userInfo->user_platform_openid,
            'platform' => $userInfo->user_platform
        ])->find();
        if (!$userPlatform) {

            /** 微信平台unionId */
            if ( in_array($userInfo->platform, [SystemUser::PLATFORM_WXAPP, SystemUser::PLATFORM_WECHAT]) && $userInfo->unionId ) {
                $user = SystemUser::where([
                    'unionid' => $userInfo->unionId,
                    'is_deleted' => 0,
                ])->find();
            }else{
                if($userInfo->phone){
                    $map = [
                        'phone' => $userInfo->phone,
                        'is_deleted' => 0,
                    ];
                }else{
                    $map = [
                        'username' => $userInfo->username,
                        'is_deleted' => 0,
                    ];
                }
                $user = SystemUser::where($map)->find();
            }

        } else {
            $user = $userPlatform->user;
        }

        return $user;
    }


    /**
     * @param SystemUser $user
     * @param $platform_openid
     * @param $platform
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function setPluginUserInfo(SystemUser $user,$platform_openid,$platform)
    {
        // 用户信息表
        $user_info = SystemUserInfo::where([
            'user_id' => $user->id,
            'is_deleted' => 0,
        ])->find();
        if (!$user_info) {

            $user_info = new SystemUserInfo();
            $user_info->user_id = $user->id;
            $user_info->platform_openid= $platform_openid;
            $user_info->platform = $platform;
            $user_info->is_deleted = 0;
        }

        if (!$user_info->save()) {
            throw new \Exception('授权登录失败！');
        }

    }


    /**
     * @param $user
     * @param bool $register
     */
    public function triggerEvent(SystemUser $user, $register = false)
    {
        $event = new UserEvent($user);
        $event->sender = $this;
        if ($register) {
            app()->event->trigger(SystemUser::EVENT_REGISTER, $event);
        }
        app()->event->trigger(SystemUser::EVENT_LOGIN, $event);

    }


}
