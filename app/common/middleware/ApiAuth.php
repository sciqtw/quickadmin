<?php
declare (strict_types = 1);

namespace app\common\middleware;

use app\common\model\SystemUser;
use app\common\model\SystemUserToken;

class ApiAuth
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle($request, \Closure $next)
    {
        $token = $request->header('x-token');
        $parent_id = (int)$request->header('X-User-Id');
        if($token){
            $tokenInfo = SystemUserToken::where("token",$token)->find();
            if($tokenInfo){
                $user = SystemUser::with(['userInfo'])->where("id",$tokenInfo->user_id)->find();
                if($user){
                    $request->user = $user;
                    $request->user_id = $user->id;

//                    // 绑定推荐关系
//                    if(
//                        empty($user->userInfo->temp_parent_id)
//                        && empty($user->userInfo->parent_id)
//                        && !empty($parent_id)
//                        && $parent_id != $user->id
//                    ){
//
//                        SystemUserInfo::bindParent($parent_id,$user->id);
//
//                    }
                }
            }



        }

        return $next($request);
    }
}
