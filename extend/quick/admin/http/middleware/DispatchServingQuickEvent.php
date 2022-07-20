<?php
declare (strict_types = 1);

namespace quick\admin\http\middleware;

use quick\admin\events\ServingQuick;
use quick\admin\Quick;

class DispatchServingQuickEvent
{
    /**
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        Quick::dispatchResource();
        return $next($request);
    }


}
