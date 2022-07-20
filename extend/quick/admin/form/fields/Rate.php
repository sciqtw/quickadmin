<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Rate extends Field
{


    public $component = 'form-rate-field';


    /**
     * 允许半选
     *
     * @return $this
     */
    public function allowHalf()
    {
        $this->attribute("allow-half", true);
        return $this;
    }

    /**
     * 低分和中等分数的界限值，值本身被划分在低分中
     * @param int $num
     * @return $this
     */
    public function low(int $num)
    {
        $this->attribute("low-threshold", $num);
        return $this;
    }

    /**
     * 辅助文字的颜色
     * @param string $color
     * @return $this
     */
    public function textColor(string $color)
    {
        $this->attribute("text-color", $color);
        return $this;
    }

    /**
     * 辅助文字数组
     *
     * @param array $texts ['极差', '失望', '一般', '满意', '惊喜']
     * @return $this
     */
    public function texts(array $texts)
    {
        $this->attribute("texts", $texts);
        $this->showText();
        return $this;
    }

    /**
     * @return $this
     */
    public function showText()
    {
        $this->attribute("show-text", true);
        return $this;
    }

    /**
     * @param array $icons
     * @return $this
     */
    public function iconClasses(array $icons)
    {
        $this->attribute("icon-classes", $icons);
        return $this;
    }

    /**
     * 指定了未选中时的图标类名
     *
     * @param $iconClass
     * @return $this
     */
    public function voidIcon($iconClass)
    {
        $this->attribute("void-icon-class", $iconClass);
        return $this;
    }

    /**
     * 显示分数
     *
     * @return $this
     */
    public function showScore()
    {
        $this->attribute("show-score", true);
        return $this;
    }

    /**
     * 高分和中等分数的界限值，值本身被划分在高分中
     * @param int $num
     * @return $this
     */
    public function high(int $num)
    {
        $this->attribute("high-threshold", $num);
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
