<?php
declare (strict_types = 1);

namespace quick\admin\http\middleware;

use quick\admin\events\ServingQuick;
use quick\admin\Quick;

class BootToolsMiddleware
{
    /**
     * @param $request
     * @param $next
     * @return mixed
     */
    public function handle($request, $next)
    {
        Quick::bootTools($request);
        return $next($request);
    }
}
