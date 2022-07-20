<?php
declare (strict_types = 1);

namespace quick\admin\http\middleware;

use components\attachment\src\resource\Attachment;
use plugins\example\QuickService;
use quick\admin\Quick;
use quick\admin\QuickPluginService;
use think\Exception;

class ServeQuickMiddleware
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        $this->bootModuleQuick();
        return $next($request);
    }


    public function bootModuleQuick()
    {
        // 获取模块启动文件
        $path =  app_path()."QuickService.php";
        if(is_file($path)){
            $resource = str_replace(
                ['/', '.php'],
                ['\\', ''],
                strAfter($path,  root_path() )
            );
            try {
                /** @var QuickPluginService $service */
                invoke($resource)->boot();
            }catch (\Exception $e){

            }
        }
    }

}
