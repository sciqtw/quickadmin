<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class InputNumber extends Field
{


    public $component = 'form-input-number-field';

    /**
     * @var int
     */
    public $default = 1;

    /**
     * 值类型
     * @var string
     */
    public $valueType = 'number';


    /**
     * @param $min
     * @return $this
     */
    public function min($min)
    {
        $this->attribute("min",$min);
        return $this;
    }

    /**
     * @param $max
     * @return $this
     */
    public function max($max)
    {
        $this->attribute("max",$max);
        return $this;
    }

    /**
     *
     *
     * @param $step
     * @return $this
     */
    public function step($step)
    {
        $this->attribute("step",$step);
        return $this;
    }

    /**
     * 严格步数 只能输入步数的倍数。
     *
     * @return $this
     */
    public function stepStrictly()
    {
        $this->attribute("step-strictly",true);
        return $this;
    }

    /**
     * 数值精度
     *
     * @param int $num
     * @return $this
     */
    public function precision(int $num)
    {
        $this->attribute("precision",$num);
        return $this;
    }

    /**
     * 按钮显示在右边
     *
     * @return $this
     */
    public function right()
    {
        $this->attribute("controls-position","right");
        return $this;
    }

    /**
     * 输入框关联的label文字
     *
     * @param string $value
     * @return $this
     */
    public function label(string $value)
    {
        $this->attribute("controls-position",$value);
        return $this;
    }

    /**
     * 默认值
     * @param number $value
     * @return $this|Field
     */
    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
