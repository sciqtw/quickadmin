<?php
declare (strict_types=1);

namespace app\common\service\common;

use app\common\model\SystemAdminInfo;
use app\common\model\SystemUser;
use app\common\model\SystemUserIdentity;
use app\common\service\CommonService;
use quick\admin\Exception;
use quick\admin\library\tools\CodeTools;
use think\db\Query;

/**
 * Class CommonAdminUserService
 * @package app\common\service\common
 */
class CommonAdminUserService extends CommonService
{


    /** @var string 账户名称 */
    public $username;

    /** @var string 昵称 */
    public $nickname;

    /** @var string 头像 */
    public $avatar;

    /** @var string 电子邮箱 */
    public $email;

    /** @var string 手机号 */
    public $phone;

    /** @var string 密码 */
    public $password;

    /** @var string 密码盐 */
    public $salt;

    /** @var boolean 状态 1 */
    public $status;


    /**
     * 验证规则
     * @return array
     */
    protected function rules(): array
    {
        return [
            'username' => 'require|max:50',
            'nickname' => 'max:50',
            'avatar' => 'max:150',
            'email' => 'max:100',
            'phone' => 'max:15',
            'password' => 'max:32',
        ];
    }


    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [
            'username' => '账户名称',
            'nickname' => '昵称',
            'avatar' => '头像',
            'email' => '电子邮箱',
            'phone' => '手机号',
            'password' => '密码',
            'salt' => '密码盐',
            'status' => '状态',
        ];
    }


    protected function checkScene(): array
    {
        return [
            'create' => ['username','password','nickname','phone']
        ];
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function createAdminUser()
    {

        if (!$this->validate('create')) {
            return $this->error($this->getFirstError());
        }

        $user = SystemUser::hasWhere('identity',function(Query $query){
            $query->where('is_admin','=',0)->hidden(['is_admin']);
        })->where('username',$this->username)->find();
        if($user){
            throw new \Exception('账号已存在');
        }

        $salt = CodeTools::random(5,2);
        $user = new SystemUser();
        $user->username = $this->username;
        $user->salt = $salt;
        $user->password = SystemUser::hashPassword($this->password,$salt);
        $user->status = 1;
        $user->avatar = '';
        $user->phone = $this->phone;

        $res = $user->save();

        if(!$res){
            throw new \Exception($user->getErrorMsg());
        }


        $userIdentity = new SystemUserIdentity();
        $userIdentity->user_id = $user->id;
        $userIdentity->is_admin = 1;
        $res = $userIdentity->save();
        if(!$res){
            throw new \Exception($userIdentity->getErrorMsg());
        }


        $adminInfo = SystemAdminInfo::where(['user_id' => $user->id ])->find();
        if(!$adminInfo){
            $adminInfo = new SystemAdminInfo();
        }
        $adminInfo->user_id = $user->id;
        $adminInfo->auth_set = '';
        $res = $adminInfo->save();
        if(!$res){
            throw new \Exception($adminInfo->getErrorMsg());
        }

        return $this->success('success');

    }


}
