<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Color extends Field
{


    public $component = 'form-color-field';

    /**
     * @var
     */
    public $default;


    /**
     * 辅助文字的颜色
     * @param string $color
     * @return $this
     */
    public function textColor(string $color)
    {
        $this->props("text-color", $color);
        return $this;
    }

    /**
     * 预定义颜色
     * @param array $colors
     * @return $this
     */
    public function predefine(array $colors)
    {
        $this->props("predefine", $colors);
        return $this;
    }

    /**
     * 支持透明度选择
     *
     * @return $this
     */
    public function alpha()
    {
        $this->props("show-alpha", true);
        return $this;
    }

    /**
     * 写入 v-model 的颜色的格式
     *
     * @param $format hsl|hsv|hex|rgb
     * @return $this
     */
    public function format($format)
    {
        $this->props("color-format", $format);
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
