<?php
declare (strict_types = 1);

namespace quick\admin\http\middleware;

use quick\admin\Quick;

class DispatchAssetsQuickEvent
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        Quick::dispatchAssets();
        return $next($request);
    }


}
