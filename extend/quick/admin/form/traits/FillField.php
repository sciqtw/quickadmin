<?php
declare (strict_types=1);

namespace quick\admin\form\traits;

/**
 * 赋值
 *
 * Trait FillField
 * @package quick\form\traits
 */
trait FillField
{

    /**
     * 字段是否可为空。
     *
     * @var bool
     */
    protected $nullable = false;

    /**
     * 替换为空值.
     *
     * @var array
     */
    protected $nullValues = [''];

    /**
     * @var
     */
    protected $fillCallback;


    public function transform($value)
    {
        return $value;
    }


    /**
     * @param  $inputs
     * @param $model
     * @return mixed|void
     */
    public function fill($inputs, $model)
    {

        return $this->fillInto($inputs, $model, $this->column);
    }


    /**
     * @param  $inputs
     * @param object $model 赋值对象
     * @param string $column 赋值变量名
     * @param null $requestColumn 请求变量名
     * @return mixed|void
     */
    public function fillInto($inputs, $model, $column, $requestColumn = null)
    {
        return $this->fillColumn($inputs, $requestColumn ?? $this->column, $model, $column);
    }

    /**
     *
     * @param  $inputs
     * @param $requestColumn
     * @param $model
     * @param $column
     * @return mixed|void
     */
    protected function fillColumn($inputs, $requestColumn, $model, $column)
    {

        return $this->fillColumnFromInputs($inputs, $model, $requestColumn, $column);
    }

    /**
     *
     *
     * @param  $inputs
     * @param object $model 数据对象
     * @param string $requestColumn 请求变量名
     * @param string $column 赋值变量名
     * @return $this
     */
    protected function fillColumnFromInputs($inputs, $model, string $requestColumn, string $column)
    {

        if (isset($inputs[$requestColumn])) {

            $inputs[$requestColumn] = $this->transform($inputs[$requestColumn]);

            if ($this->fillCallback instanceof \Closure) {
                $value = call_user_func($this->fillCallback, $inputs, $model, $column, $requestColumn);
            } else {
                $value = $this->getValueFormInputs($inputs, $requestColumn);
            }
            $model->{$column} = $this->isNullValue($value) ? '' : $value;
        }
        return $this;
    }


    /**
     *
     * @param  $inputs
     * @param string $column
     * @return mixed
     */
    protected function getValueFormInputs($inputs, string $column)
    {
        return data_get($inputs, str_replace('->', '.', $column));
    }


    /**
     * 判断值是否为空
     *
     * @param $value
     * @return bool
     */
    protected function isNullValue($value)
    {
        if (!$this->nullable) {
            return false;
        }
        return is_callable($this->nullValues)
            ? ($this->nullValues)($value)
            : in_array($value, (array)$this->nullValues);
    }

    /**
     * 指定一个回调，该回调应用于对字段的model属性进行赋值.
     *
     * @param callable $fillCallback
     * @return $this
     */
    public function fillUsing($fillCallback)
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }


    /**
     * 设置固定保存值
     *
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->fillUsing(function () use ($value) {
            return $value;
        });
        return $this;
    }


}
