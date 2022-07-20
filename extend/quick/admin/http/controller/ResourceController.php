<?php
declare (strict_types=1);

namespace quick\admin\http\controller;


use quick\admin\contracts\AuthInterface;
use quick\admin\http\InteractsWithResources;
use quick\admin\http\response\JsonResponse;
use quick\admin\library\service\AuthService;
use think\exception\HttpException;
use quick\admin\Resource;
use quick\admin\Quick;

class ResourceController
{
    use InteractsWithResources;


    /**
     * @param string $resource
     * @param string $action
     * @param string $func
     * @return bool|mixed|\think\response\Json|\think\response\Redirect
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function execute($resource = '', $action = '', $func = '')
    {

        $auth = Quick::getAuthService();

        if (empty($resource) || empty($action)) {
            throw new HttpException(500, lang('resource can not be empty'));
        }

        $resourceClass = Quick::resourceForKey(app('http')->getName(), $resource);
        if (empty($resourceClass)) {
            throw new HttpException(500, lang('resource can not be empty'));
        }

        app()->request->setAction($action);

        /** @var Resource $instance */
        $instance = invoke($resourceClass);
        $vars = [];


        $instanceType = 'resource';
        if (is_callable([$instance, $action])) {
//            if (!in_array($action, ['index', 'filter', 'list'])) {
//                throw new HttpException(404, lang("Resource does not allow access to this method: %s", [$action . "()"]));
//            }
            // 执行资源类操作方法
            $call = [$instance, $action];
        } elseif (is_callable([$instance, '_empty'])) {
            // 空操作
            $call = [$instance, '_empty'];
            $vars = [$action];
        } else {

            $actionInstance = $instance->handleResourceAction($action, $func);

            if (is_callable([$actionInstance, $func]) && in_array($func, ['load', 'store', 'async'])) {

                // todo $func模式有利于后期扩展
                $call = [$actionInstance, $func];
                $instanceType = 'action';
            } else {
                throw new HttpException(404, lang('resource action %s not found', [get_class($actionInstance) . '->' . $func . '()']));
            }
        }



        $request = app()->request;
        $checkInstance = $call[0];
        $checkAction = $call[1];
        if ($instanceType === 'action') {
            $checkAction = '';
        }



        $res = $auth->checkAuth($checkInstance, $checkAction, $request);
        if ($res !== true) {
            return $res;
        }


        $res = invoke($call, $vars);
        if ($res instanceof JsonResponse) {
            return $res->send();
        }
        return $res;
    }




}
