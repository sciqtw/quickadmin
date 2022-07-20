<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Field;
use think\helper\Arr;

class Date extends FieldFilter
{

    /**
     * {@inheritdoc}
     */
    protected $query = 'whereTime';

    /**
     * @var string
     */
    protected $operator = '=';

    protected $callback;


    public function condition($inputs)
    {
        $data = filter_params();
        $value = $this->getField()->resolve($data)->getValue();

        empty($value) && $value =  $data[$this->requestColumn] ?? '';

        if (empty($value)) {

            return false;
        }

        if (is_callable($this->callback)) {
            $resolveCallback = \Closure::bind($this->callback, $this);
            $value = call_user_func($resolveCallback, $value);
        }else if (is_array($value)) {
            $value = [
                $this->_formatTime($value[0]),
                $this->_formatTime($value[1]),
            ];
        } else {
            $value = $this->_formatTime($value);
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->operator, $this->value);
    }


    /**
     * 格式化时间
     * @param $value
     * @return float|int
     */
    protected function _formatTime($value)
    {
        if (is_numeric($value)) {
            $value = strlen((string)$value) > 11 ? $value / 1000 : $value;
        } else {
            $value = empty($value) ? '' : strtotime($value);
        }
        return $value;
    }


    /**
     * @param string $rengeSeparator
     * @return $this
     */
    public function daterange(string $rengeSeparator = '-')
    {
        $this->callback = function ($value){
            $value = [
                date("Y-m-d H:i:s",$this->_formatTime($value[0])),
                date("Y-m-d 23:59:59",$this->_formatTime($value[1])),
            ];
            return $value;
        };
        $this->getField()->daterange()->rangeSeparator($rengeSeparator);
        return $this->between();
    }


    /**
     * @param string $rengeSeparator
     * @return $this
     */
    public function monthrange(string $rengeSeparator = '-')
    {
        $this->callback = function ($value){
            $value = [
                date("Y-m-d H:i:s",$this->_formatTime($value[0])),
                date("Y-m-d 23:59:59",strtotime('+1 month -1 day',$this->_formatTime($value[1]))),
            ];
            return $value;
        };
        $this->getField()->monthrange()->rangeSeparator($rengeSeparator);
        return $this->between();
    }


    /**
     * @param string $rengeSeparator
     * @return $this
     */
    public function datetimerange(string $rengeSeparator = '-')
    {
        $this->callback = function ($value){
            $value = [
                $this->_formatTime($value[0]),
                $this->_formatTime($value[1])
            ];
            return $value;
        };
        $this->getField()->datetimerange()->rangeSeparator($rengeSeparator);
        return $this->between();
    }


    private function between()
    {
        $this->operator = 'between';
        return $this;
    }


    /**
     * 小于
     * @return $this
     */
    public function lt()
    {
        $this->operator = '<';
        return $this;
    }

    /**
     * 小于等于
     * @return $this
     */
    public function nlt()
    {
        $this->operator = '<=';
        return $this;
    }

    /**
     * 大于
     * @return $this
     */
    public function gt()
    {
        $this->operator = '>';
        return $this;
    }


    /**
     * 大于等于
     * @return $this
     */
    public function ngt()
    {
        $this->operator = '>=';
        return $this;
    }

    /**
     * 设置默认表单字段
     *
     * @return Field
     */
    protected function defaultField()
    {
        return $this->setField(\quick\admin\form\fields\Date::make($this->requestColumn, $this->label));
    }

}
