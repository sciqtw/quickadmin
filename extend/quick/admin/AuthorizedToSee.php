<?php

namespace quick\admin;

use Closure;
use think\Request;

trait AuthorizedToSee
{
    /**
     * @var
     */
    public $seeCallback;

    /**
     * @param Request $request
     * @return bool|mixed
     */
    public function authorizedToSee(Request $request)
    {
        return $this->seeCallback ? call_user_func($this->seeCallback, $request) : true;
    }

    /**
     * @param Closure $callback
     * @return $this
     */
    public function canSee(Closure $callback)
    {
        $this->seeCallback = $callback;

        return $this;
    }
}
