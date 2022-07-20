<?php


namespace quick\http\middleware;


use app\Request;
use think\facade\Config;
use think\Response;

/**
 * 跨域中间件
 * Class AllowOriginMiddleware
 * @package app\http\middleware
 */
class AllowOriginMiddleware
{

    /**
     * @var array
     */
    protected $header = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Headers' => 'Authorization,Content-Type,If-Match,If-Modified-Since,If-None-Match,If-Unmodified-Since,X-Requested-With,User-Form-Token,User-Token,Token',
        'Access-Control-Allow-Methods' => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Expose-Headers' => 'User-Form-Token,User-Token,Token',
        'Access-Control-Allow-Credentials' => 'true',
    ];


    /**
     * 允许跨域的域名
     * @var string
     */
    protected $cookieDomain;

    /**
     * @param Request $request
     * @param \Closure $next
     * @return Response
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->cookieDomain = Config::get('cookie.domain', '');
        $origin = $request->header('origin');
        $header = $this->header;

        $request->filter(['htmlspecialchars', 'strip_tags', 'trim']);

        if ($origin && ('' != $this->cookieDomain && strpos($origin, $this->cookieDomain))){
            $header['Access-Control-Allow-Origin'] = $origin;
        }

        if ($request->isOptions()) {
            return response()->code(204)->header($header);
        }

        return $next($request)->header($header);
    }
}