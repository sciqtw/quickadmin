<?php
declare (strict_types=1);

namespace app\common\middleware;


use quick\admin\Quick;
use think\exception\ClassNotFoundException;
use think\Request;

class AdminAuth
{

    /**
     * @param Request $request
     * @param \Closure $next
     * @return bool|mixed|\think\response\Json|\think\response\Redirect
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle(Request $request, \Closure $next)
    {


        $controller = request()->controller();
        $controller = $this->controller($controller);

        $action = (string)request()->action();
        $suffix = config('route.action_suffix');
        $action = $action . $suffix;

        $auth = Quick::getAuthService();
        $res = $auth->checkAuth($controller, $action, $request);
        if ($res !== true) {
            return $res;
        }
        return $next($request);
    }



    /**
     * 实例化访问控制器
     * @access public
     * @param string $name 资源地址
     * @return object
     * @throws ClassNotFoundException
     */
    public function controller(string $name)
    {

        $suffix = config('route.controller_suffix') ? 'Controller' : '';
        $controllerLayer = config('route.controller_layer') ?: 'controller';

        $class = app()->parseClass($controllerLayer, $name . $suffix);
        if (class_exists($class)) {
            return app()->make($class, [], true);
        }

        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }
}
