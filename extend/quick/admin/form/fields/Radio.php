<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class Radio extends Field
{


    public $component = 'form-radio-field';


    protected $valueType = 'string';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * 禁用
     *
     * @var array
     */
    protected $disabled = [];

    /**
     * @var boolean
     */
    protected $border;

    /**
     * @var string
     */
    protected $optName;

    /**
     * @var string
     */
    protected $optSize;


    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {

        $this->options = $options;
        return $this;
    }

    /**
     * 是否显示边框
     *
     * @param $border
     * @return $this
     */
    public function border()
    {
        $this->border = true;
        return $this;
    }

    /**
     * radio size
     *
     * @param string $size
     * @return $this
     */
    public function optSize(string $size)
    {
        $this->optSize = $size;
        return $this;
    }

    protected function getOptions()
    {
        $data = [];
        foreach ($this->options as $key => $value) {
            $attrs = [];
            $this->optName && $attrs['name'] = $this->optName;
            $this->border && $attrs['border'] = $this->border;
            $this->optSize && $attrs['size'] = $this->optSize;
            in_array($key, $this->disabled) && $attrs['disabled'] = true;
            $data[] = [
                "key" => (string)$key,
                "label" => $value,
                "attrs" => $attrs,
            ];
        }
        return $data;
    }

    /**
     * 禁用
     *
     * @param array|string $key
     * @return $this|Field
     */
    public function optDisabled($key)
    {
        if (is_array($key)) {
            $this->disabled = array_merge($this->disabled, $key);
        } else {
            $this->disabled = array_merge($this->disabled, [$key]);
        }
        return $this;
    }

    /**
     * 按钮形式的 Radio 激活时的文本颜色
     *
     * @param string $color
     * @return $this
     */
    public function textColor(string $color)
    {
        $this->attribute("text-color", $color);
        return $this;
    }

    /**
     * 按钮形式的 Radio 激活时的填充色和边框色
     * @param string $color
     * @return $this
     */
    public function fillColor(string $color)
    {
        $this->attribute("fill", $color);
        return $this;
    }

    public function radioButton()
    {
        $this->attribute("radioButton", true);
        return $this;
    }

    /**
     * @return string
     */
    protected function getDefaultValue()
    {
        return (string)($this->value !== null ? $this->value : $this->getDefault());
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->attribute("options", $this->getOptions());
        return array_merge(parent::jsonSerialize(), []);
    }
}
