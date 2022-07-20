<?php
declare (strict_types=1);

namespace quick\admin\form\traits;


/**
 *  解析字段值
 * Trait Resolve
 * @package Quick\form\traits
 */
trait Resolve
{


    /**
     * 用于解析字段显示值的回调.
     *
     * @var \Closure
     */
    protected $displayCallback;

    /**
     * 用于解析字段值的回调.
     *
     * @var \Closure
     */
    protected $resolveCallback;

    /**
     * @var
     */
    protected $value;

    /**
     * 解析要显示的字段值.
     *
     * @param mixed $resource
     * @param string|null $Column
     * @return void
     */
    public function resolveForDisplay($resource, $column = null)
    {
        $column = $column ?? $this->column;

        if (!$this->displayCallback) {
            $this->resolve($resource, $column);
        } elseif (is_callable($this->displayCallback)) {
            $value = data_get($resource, str_replace('->', '.', $column), $placeholder = new \stdClass());

            if ($value !== $placeholder) {
                $this->value = call_user_func($this->displayCallback, $value);
            }
        }
    }


    /**
     * 解析字段值
     *
     * @param $model
     * @param null $column
     * @return $this
     */
    public function resolve($model, $column = null)
    {
        $column = $column ?? $this->column;

        if (!is_callable($this->resolveCallback)) {

            $this->value = $this->resolveColumn($model, $column,$this->default);
        } elseif (is_callable($this->resolveCallback)) {
            $value =  $this->resolveColumn($model, $column,$placeholder = new \stdClass());
            if ($value === $placeholder) {
                $value = $this->getDefault();
            }
            $resolveCallback = \Closure::bind($this->resolveCallback, $this);
            $this->value = call_user_func($resolveCallback, $value, $model);
        }
        return $this;
    }


    /**
     * 从给定资源解析给定属性.
     *
     * @param mixed $resource
     * @param string $column
     * @param  $default
     * @return mixed
     */
    protected function resolveColumn($resource, $column, $default = '')
    {
        $value = data_get($resource, str_replace('->', '.', $column), $default);
        if($value === $default && strpos($column,".")){
            $value = isset($resource[$column]) ? $resource[$column]:$default;
        }
        return $value;
    }

    /**
     * 定义应用于解析字段显示值的回调.
     *
     * @param callable $displayCallback
     * @return $this
     */
    public function displayUsing(callable $displayCallback)
    {
        $this->displayCallback = $displayCallback;

        return $this;
    }


    /**
     * 定义应用于解析字段值的回调.
     *
     * @param callable $resolveCallback
     * @return $this
     */
    public function resolveUsing(callable $resolveCallback)
    {
        $this->resolveCallback = $resolveCallback;

        return $this;
    }

}
