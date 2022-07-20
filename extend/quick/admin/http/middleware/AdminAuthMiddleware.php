<?php


namespace quick\admin\http\middleware;


use app\Request;
use Doctrine\Common\Annotations\AnnotationReader;
use quick\admin\annotation\AdminAuth;
use quick\admin\http\response\JsonResponse;
use quick\admin\library\service\AuthService;
use think\exception\ClassNotFoundException;
use think\helper\Str;
use think\Response;

/**
 * 跨域中间件
 * Class AllowOriginMiddleware
 * @package app\http\middleware
 */
class AdminAuthMiddleware
{


    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed|\think\response\Json|\think\response\Redirect
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle(Request $request, \Closure $next)
    {

        $auth = AuthService::instance();
        $request->auth = $auth;
//        $module = app()->http->getName();
        // todo 判断模块是否独立后台，如果是则跳转到独立登录页面
        $module = 'admin';

        if($auth->check()){
            return $next($request);
        }elseif($auth->isLogin()){
            return JsonResponse::make()->error('你无权操作',[],413)->send();
        }else{

            if($request->isAjax()){

                return JsonResponse::make()->error('请先登录获取权限',[
                    'redirect' => (string)url($module.'/index/login')
                ],419)->send();
            }
            return redirect((string)url($module.'/index/login'));
        }

    }


    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check()
    {

        $controller =  request()->controller();
        $controller = $this->controller($controller);

        $action =  (string)request()->action();
        $suffix = config('action_suffix');

        $action = $action . $suffix;
        $this->checkAuth($controller,$action);


    }

    /**
     * 获取当前节点内容
     * @param string $type
     * @return string
     */
    public function getCurrent(string $type = ''): string
    {
        $space = app()->getNamespace();
        $prefix = strtolower(app()->http->getName());
        if (preg_match("|\\\\plugins\\\\{$prefix}$|", $space)) {
            $prefix = "plugins-{$prefix}";
        }

        // 获取应用前缀节点
        if ($type === 'module') {
            return $prefix;
        }
        // 获取控制器前缀节点
        $route = app()->request->route();
        $middle = isset($route['resource']) ? 'resource/' . $route['resource'] : Str::snake(app()->request->controller());
        if ($type === 'controller') {
            return $prefix . '/' . $middle;
        }

        // 获取完整的权限节点
        if (isset($route['resource'])) {
            return $prefix . '/' . $middle . '/' . $route['action'];
        }
        return $prefix . '/' . $middle . '/' . strtolower(app()->request->action());
    }


    /**
     * 使用注解实现权限控制
     * @param $object
     * @param $action
     * @return bool
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function checkAuth($object,$action)
    {
        $class = new \ReflectionClass($object);
        $method = $class->getMethod($action);

        $reader = new AnnotationReader();
        /** @var AdminAuth $annotation */
        $annotation = $reader->getMethodAnnotation($method,AdminAuth::class);
        if($annotation
            && !empty($methods = $annotation->getMethod())
            && in_array(request()->method(),$methods)
        ){
            $node =  $annotation->getNode($this->getCurrent());

            $nodes = AuthService::instance()->nodes();
            dump($nodes);
            dump($methods);
            if(in_array($node,$nodes)){
                echo "----";
            }
            echo $this->getCurrent();
            return false;
        }
        return true;
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
