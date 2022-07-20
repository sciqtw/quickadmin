<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class Checkbox extends Field
{


    public $component = 'form-checkbox-field';

    public $valueType = 'array';

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
     * 默认值
     * @var array
     */
    protected $default = [];


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
     *
     * @param int $min
     * @return $this
     */
    public function min(int $min)
    {
        $this->attribute("min",$min);
        return $this;
    }

    /**
     * @param int $max
     * @return $this
     */
    public function max(int $max)
    {
        $this->attribute("max",$max);
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
            $attrs['disabled'] = false;
            in_array($key, $this->disabled) && $attrs['disabled'] = true;
            $data[] = [
                "key" => $key,
                "name" => $value,
                "disabled" =>  $attrs['disabled'],
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

    public function checkButton()
    {
        $this->attribute("checkButton", true);
        return $this;
    }


    /**
     * 显示全选按钮
     *
     * @return $this
     */
    public function showCheckAll()
    {
        $this->attribute("showCheckAll", true);
        return $this;
    }

    /**
     * 设置默认值
     * @param $value
     * @return $this
     */
    public function default($value)
    {

        if(empty($value)){
            $value = [];
        }else{
            is_string($value) && $value = [$value];
        }

        $this->default = $value;
        return $this;
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
