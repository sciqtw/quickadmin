<?php

namespace quick\admin\table\traits;


use Closure;
use quick\admin\Resource;
use think\Exception;

/**
 * Trait HasActionColumnTrait
 * @package quick\admin\table\column
 */
trait HasActionColumnTrait
{

    /**
     * 是否可以编辑回调
     * @var Closure
     */
    protected $canRunCallback;

    /**
     *
     * @var Closure
     */
    protected $handleCallback;

    /**
     *
     * @var Closure
     */
    protected $resolveCallback;


    /**
     * @var bool
     */
    protected $authorizedToEdit = false;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * 主键提参key
     *
     * @var string
     */
    protected static $keyValue = "_keyValue_";

    /**
     * col提参key
     *
     * @var string
     */
    protected static $colValue = "_colValue_";

    /**
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * @return Resource
     * @throws Exception
     */
    public function getResource(): Resource
    {
        if(!$this->resource){
            throw new Exception("Resource is not set");
        }
        return $this->resource;
    }

    public function getModel()
    {
        return $this->getResource()::newModel();
    }

    /**
     * 设置权限回调
     *
     * @param Closure $closure
     * @return $this
     */
    public function canRun(Closure $closure)
    {
        $this->canRunCallback = $closure;
        return $this;
    }

    /**
     * 业务逻辑回调
     *
     * @param Closure $closure
     * @return $this
     */
    public function handleUsing(Closure $closure)
    {
        $this->resolveCallback = $closure;
        return $this;
    }

    /**
     * 异步数据处理回调
     *
     * @param Closure $closure
     * @return $this
     */
    public function resolveUsing(Closure $closure)
    {
        $this->handleCallback = $closure;
        return $this;
    }

    /**
     * 是否可以编辑
     *
     * @return bool
     */
    public function isCanRun(): bool
    {
        if ($this->canRunCallback) {
            if (!call_user_func($this->canRunCallback, request(), $this->value)) {
                $this->authorizedToEdit = false;
                return false;
            }
        }
        $this->authorizedToEdit = true;
        return true;
    }





}
