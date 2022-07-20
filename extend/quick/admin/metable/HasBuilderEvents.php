<?php
declare (strict_types=1);

namespace quick\admin\metable;


use Closure;

trait HasBuilderEvents
{


    /**
     * @var array
     */
    protected static $elementEventCallbacks = [];

    /**
     * @var string 初始化事件key
     */
    protected static $initEventKey = "init_event";

    /**
     * @param Closure|null $callback
     */
    public static function initEvent(Closure $callback = null)
    {
        static::$elementEventCallbacks[static::$initEventKey][] = $callback;
    }


    /**
     *
     */
    protected function callInitCallbacks()
    {
        if (!isset(static::$elementEventCallbacks[static::$initEventKey])
            || empty(static::$elementEventCallbacks[static::$initEventKey])) {
            return;
        }

        foreach (static::$elementEventCallbacks[static::$initEventKey] as $callback) {
            call_user_func($callback, $this);
        }
    }


}
