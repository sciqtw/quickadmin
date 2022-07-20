<?php
declare (strict_types = 1);
/**
 *  控制器基类
 */

namespace app\common\controller;


use quick\admin\annotation\AdminAuth;
use quick\admin\http\middleware\AppModuleRunMiddleware;
use think\App;

class Backend
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
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [
        \app\common\middleware\AdminAuth::class
    ];



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

    // 初始化
    protected function initialize()
    {

    }

    /**
     * @AdminAuth(title="responseJson",auth=false,menu=false,login=false)
     * @param $data
     * @return \think\response\Json
     */
    public function responseJson($data)
    {
        if(!isset($data['code'])){
            $data = [
                'code' => 0,
                'msg' => '',
                'data' => $data,
            ];
        }
        return json($data);
    }
}
