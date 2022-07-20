<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Time extends Field
{


    public $component = 'form-time-field';

    /**
     * @var
     */
    public $default;

    /**
     * @var array
     */
    public $pickerOptions = [];

    public $valueType = 'date';

    /**
     * 隐藏清除按钮
     *
     * @return $this
     */
    public function hideClear()
    {
        $this->attribute('clearable', false);
        return $this;
    }

    /**
     * 仅读
     *
     * @return $this|Field
     */
    public function readonly()
    {
        $this->attribute('readonly', true);
        return $this;
    }

    /**
     * 输入框不可编辑
     *
     * @return $this
     */
    public function notEditable()
    {
        $this->attribute('editable', false);
        return $this;
    }

    /**
     * 范围选择时开始日期的占位内容
     *
     * @param string $placeholder
     * @return $this
     */
    public function startPlaceholder(string $placeholder)
    {
        $this->attribute('start-placeholder', $placeholder);
        return $this;
    }

    /**
     * 范围选择时开始日期的占位内容
     *
     * @param string $placeholder
     * @return $this
     */
    public function endPlaceholder(string $placeholder)
    {
        $this->attribute('end-placeholder', $placeholder);
        return $this;
    }

    /**
     * 是否为时间范围选择，仅对<el-time-picker>有效
     *
     * @return $this
     */
    public function isRange()
    {
        $this->attribute('is-range', true);
        $this->rangeSeparator();
        $this->valueFormat();
        return $this;
    }

    /**
     * 选择范围时的分隔符
     *
     * @param string $separator
     * @return $this
     */
    public function rangeSeparator(string $separator = '-')
    {
        $this->attribute('range-reparator', $separator);
        return $this;
    }

    /**
     * 使用箭头进行时间选择
     *
     * @return $this
     */
    public function arrowControl()
    {
        $this->attribute('arrow-control', true);
        return $this;
    }

    /**
     * @param string $value left / center / right
     * @return $this
     */
    public function align(string $value)
    {
        $this->attribute('align', $value);
        return $this;
    }

    /**
     * isRange 模式下有效
     *
     * @param string $format
     * @return $this
     */
    public function valueFormat(string $format = 'HH:MM:SS')
    {
        $this->attribute('value-format', $format);
        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function pickerOptions(array $options)
    {
        $this->pickerOptions = $options;
        return $this;
    }

    /**
     * 自定义头部图标的类名
     *
     * @param string $iconClass
     * @return $this
     */
    public function prefixIcon(string $iconClass)
    {
        $this->attribute('prefix-icon', $iconClass);
        return $this;
    }

    /**
     * 自定义清空图标的类名
     *
     * @param string $iconClass
     * @return $this
     */
    public function clearIcon(string $iconClass)
    {
        $this->attribute('clear-icon', $iconClass);
        return $this;
    }

    /**
     * 开始时间
     *
     * @param string $start 例如：09:00
     * @return $this
     */
    public function start(string $start)
    {
        $this->pickerOptions = array_merge($this->pickerOptions, ["start" => $start]);
        return $this;
    }

    /**
     * 结束时间
     *
     * @param string $end 例如：09:00
     * @return $this
     */
    public function end(string $end)
    {
        $this->pickerOptions = array_merge($this->pickerOptions, ["end" => $end]);
        return $this;
    }

    /**
     * 间隔时间
     *
     * @param string $step 例如：00:30
     * @return $this
     */
    public function step(string $step)
    {
        $this->pickerOptions = array_merge($this->pickerOptions, ["step" => $step]);
        return $this;
    }


    /**
     * time Picker Options
     * 可选时间段，例如'18:30:00 - 20:30:00'或者传入数组['09:30:00 - 12:00:00', '14:30:00 - 18:30:00']
     *
     * @param $value
     * @return $this
     */
    public function selectableRange($value)
    {
//        $this->attribute('selectableRange',$value);
        $this->pickerOptions = array_merge($this->pickerOptions, ["selectableRange" => $value]);
        return $this;
    }

    /**
     * 时间格式化(TimePicker)
     *
     * @param string $format 'HH:mm:ss'
     * @return $this
     */
    public function format(string $format)
    {
//        $this->attribute('format',$format);
        $this->props(["format" => $format]);
        return $this;
    }



    /**
     * @return string
     */
    protected function getDefaultValue()
    {
        $value = $this->value !== null ? $this->value : $this->getDefault();
        if (is_numeric($value)) {
            $value = $value + 0;
        }
        return $value;
    }

    /**
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->props('picker-options', $this->pickerOptions);
        return array_merge(parent::jsonSerialize(), []);
    }
}
