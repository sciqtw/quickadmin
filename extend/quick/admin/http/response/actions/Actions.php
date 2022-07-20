<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;


use JsonSerializable;
use quick\admin\metable\Metable;

abstract class  Actions implements JsonSerializable
{

    /**
     * 动作
     * @var string
     */
    protected $action = 'message';

    /**
     *  延迟执行
     * @var int
     */
    protected $delay = 0;

    /**
     * 动作参数
     * @var array
     */
    protected $params = [];


    /**
     * 延迟执行 毫秒
     * @param int $delay 毫秒
     * @return $this
     */
    public function delay(int $delay)
    {
        $this->delay = $delay;
        return $this;
    }


    /**
     * @param array $params
     * @return $this
     */
    public function withParams(array $params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }


    /**
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'action' => $this->action,
            'delay' => $this->delay,
            'params' => $this->params,
        ];
    }

}
