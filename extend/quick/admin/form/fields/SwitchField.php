<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class SwitchField extends Field
{


    public $component = 'form-switch-field';

    /**
     * @var int
     */
    protected $width = 40;

    protected $valueType = 'number';

    /**
     *
     * @param string $color
     * @return $this
     */
    public function activeColor(string $color)
    {
        $this->attribute("active-color", $color);
        return $this;
    }

    /**
     *
     * @param string $color
     * @return $this
     */
    public function inactiveColor(string $color)
    {
        $this->attribute("inactive-color", $color);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function activeText(string $text)
    {
        $this->attribute("active-text", $text);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function inactiveText(string $text)
    {
        $this->attribute("inactive-text", $text);
        return $this;
    }


    /**
     * 文字内部显示
     *  无论图标或文本是否显示在点内，只会呈现文本的第一个字符
     * @return $this
     */
    public function inline()
    {
        $this->attribute("inline-prompt", true);
        return $this;
    }



    /**
     * @param $value
     * @return $this
     */
    public function activeValue($value)
    {
        $this->attribute("active-value", $value);
        return $this;
    }



    /**
     * @param $value
     * @return $this
     */
    public function inactiveValue($value)
    {
        $this->attribute("inactive-value", $value);
        return $this;
    }


    /**
     * @param int $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;
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
        $this->attribute("width", $this->width);
        return array_merge(parent::jsonSerialize(), []);
    }
}
