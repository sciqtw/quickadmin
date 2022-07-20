<?php
declare (strict_types=1);

namespace quick\admin\http\middleware;


use think\Exception;
use think\facade\Request;

/**
 * 应用模块访问中间件
 *
 * Class AppModuleRunMiddleware
 * @package quick\admin\http\middleware
 */
class AppModuleRunMiddleware
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, \Closure $next)
    {
        $quick_app_run_key = app()->config->get('app.quick_app_run_key', '');
        if (!empty($quick_app_run_key)) {
            if (!defined('Quick_APP_RUN_KEY') || Quick_APP_RUN_KEY !== $quick_app_run_key) {
                throw new Exception('无权访问应用模块:' . app()->http->getName());
            }
        }
        return $next($request);
    }


}
