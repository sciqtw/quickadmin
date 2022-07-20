<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Slider extends Field
{


    public $component = 'form-slider-field';

    protected $range = false;

    /**
     * @return $this
     */
    public function hideTooltip()
    {
        $this->attribute("show-tooltip", false);
        return $this;
    }

    /**
     * @param $step
     * @return $this
     */
    public function step($step)
    {
        $this->attribute("step", $step);
        return $this;
    }

    /**
     * @return $this
     */
    public function showStops()
    {
        $this->attribute("show-stops", true);
        return $this;
    }

    /**
     * @return $this
     */
    public function showInput()
    {
        $this->attribute("show-input", true);
        return $this;
    }

    /**
     * @param $min
     * @param $max
     * @return $this
     */
    public function range($min = 0, $max = 0)
    {
        !empty($min) && $this->min($min);
        !empty($max) && $this->max($max);
        $this->range = true;
        $this->attribute("range", true);
        return $this;
    }

    /**
     *  Slider 变成竖向模式
     * @param int $height
     * @return $this
     */
    public function vertical(int $height = 200)
    {
        $this->attribute("vertical", true);
        $this->attribute("height", $height."px");
        return $this;
    }

    /**
     *
     * @param array $marks
     * @return $this
     */
    public function marks(array $marks)
    {
        $this->attribute("marks", $marks);
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function label(string $label)
    {
        $this->attribute("label", $label);
        return $this;
    }

    /**
     * 输入框的尺寸
     * @param string $size large|medium|small|mini
     * @return $this
     */
    public function inputSize(string $size)
    {
        $this->attribute("input-size", $size);
        return $this;
    }

    /**
     * 输入时的去抖延迟，毫秒，仅在show-input等于true时有效
     *
     * @param int $num
     * @return $this
     */
    public function debounce(int $num)
    {
        $this->attribute(__FUNCTION__, $num);
        return $this;
    }

    /**
     * @param $min
     * @return $this
     */
    public function min($min)
    {
        $this->attribute("min", $min);
        return $this;
    }

    /**
     * @param $max
     * @return $this
     */
    public function max($max)
    {
        $this->attribute("max", $max);
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

    public function getDefault()
    {
        if($this->range && !is_array($this->default)){
            throw new Exception("The slider value must be an array");
        }
        return $this->default;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
