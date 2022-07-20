<?php
declare (strict_types = 1);
/**
 *  控制器基类
 */

namespace app\common\controller;


use app\common\ApiCode;
use quick\admin\http\middleware\AppModuleRunMiddleware;
use think\App;
use think\exception\HttpResponseException;

class Api
{


    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = [];


    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;

        // 控制器初始化
        $this->initialize();
    }


    /**
     * init
     */
    protected function initialize()
    {
        $this->checkLogin();
    }


    /**
     * 权限
     * @return $this
     */
    protected function checkLogin()
    {
        if(!in_array("*",$this->noNeedLogin)){
            if(!in_array($this->request->action(),$this->noNeedLogin)){

                if(empty($this->request->user_id)){
                    throw new HttpResponseException(json([
                        'code' => ApiCode::CODE_NOT_LOGIN,
                        'msg'  => '请先登录'
                    ]));
                }
            }
        }
        return $this;
    }

    /**
     * @param $data
     * @return \think\response\Json
     */
    protected function responseJson($data)
    {
        return json($data);
    }
}
