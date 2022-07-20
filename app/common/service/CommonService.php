<?php


namespace app\common\service;


use app\common\ApiCode;
use quick\admin\BaseAction;
use think\facade\Db;
use think\Pipeline;

class CommonService extends BaseAction
{


    /**
     * 开启事务
     */
    protected function startTrans()
    {
        Db::startTrans();
    }


    /**
     * 提交事务
     */
    protected function commit()
    {
        Db::commit();
    }


    /**
     * 回滚事务
     */
    protected function rollback()
    {
        Db::rollback();
    }


    /**
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return array
     */
    protected function success(string $msg, $data = [], int $code = 0)
    {
        $code = empty($code) ? ApiCode::CODE_SUCCESS : $code;
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }


    /**
     * @param string|Null $msg
     * @param array|String $data
     * @param int $code
     * @return array
     */
    protected function error($msg, $data = [], int $code = 0)
    {
        $code = empty($code) ? ApiCode::CODE_ERROR : $code;
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }


    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return array
     */
    protected function responseData(int $code, string $msg = '', array $data = [])
    {
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
    }



    /**
     * @param array $pipes 中间件
     * @param Object|array $context 上下文
     * @param \Closure $func 核心业务逻辑
     * @return mixed
     */
    public function runMiddleware(array $pipes, $context, \Closure $func)
    {
        return $this->pipeline($pipes)
            ->send($context)
            ->then($func);
    }


    /**
     * @param array $pipes 中间件
     * @return Pipeline
     */
    public function pipeline(array $pipes)
    {
        $pipes = array_map(function ($orderPipe) {
            return function ($orderContext, $next) use ($orderPipe) {
                $call = [app()->make($orderPipe), "handle"];
                return call_user_func($call, $orderContext, $next, []);
            };
        }, $pipes);

        return (new Pipeline())->through($pipes);
    }




}
