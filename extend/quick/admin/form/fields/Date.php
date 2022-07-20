<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Date extends Field
{


    public $component = 'form-date-field';

    /**
     * @var
     */
    public $default;

    /**
     * @var array
     */
    public $pickerOptions = [];

    /**
     * @var string
     */
    public $type = 'date';

    /**
     * @return Field
     */
    public function init()
    {
        $this->_valueFormat("x");
        $this->valueType = 'number';
        $this->resolveUsing(function ($value, $model) {

            if(is_numeric($value)){
                return  $this->_formatTime($value);
            }elseif(is_array($value)){
                foreach ($value as &$item){
                    $item = $this->_formatTime($item);
                }
            }
            return $value;
        });
        return parent::init();
    }


    public function transform($value)
    {
        if(is_numeric($value)){
            return  $this->jsTimeTransform($value);
        }elseif(is_array($value)){
            foreach ($value as &$item){
                $item = $this->jsTimeTransform($item);
            }
        }
        return $value;
    }

    public function jsTimeTransform($value)
    {
        if(is_numeric($value) && strlen((string)$value) == 13){
            return $value/1000;
        }
        return $value;
    }

    /**
     * 显示类型
     *
     * @param string $type year/month/date/dates/ week/datetime/datetimerange/ daterange/monthrange
     * @return $this
     */
    public function type(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return $this
     */
    public function year()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function month()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function date()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function dates()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function week()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function datetime()
    {
        $this->type(__FUNCTION__);
        return $this;
    }

    /**
     * @return $this
     */
    public function datetimerange()
    {
        $this->type(__FUNCTION__);
        $this->setRangeResolve();
        return $this;
    }

    /**
     * @return $this
     */
    public function daterange()
    {
        $this->type(__FUNCTION__);
        $this->setRangeResolve();
        $this->valueType = 'array';
        return $this;
    }


    /**
     * 设置关联解析
     */
    private function setRangeResolve()
    {
        $this->fillUsing(function ($inputs, $model, $column, $requestColumn) {
            $value = $inputs[$requestColumn] ?? '';
            $keys = explode('@', $requestColumn);
            try {
                if (count($keys) == 2) {
                    $model->{$keys[0]} = $value[0];
                    $model->{$keys[1]} = $value[1];
                }
            }catch (\Exception $e){
                return '';
            }

            return $value;
        });

        $this->resolveUsing(function ($value, $model) {

            $keys = explode('@', $this->column);
            $value = [];
            if (count($keys) == 2) {
                !empty($model[$keys[0]]) && $value[] = $this->_formatTime($model[$keys[0]]);
                !empty($model[$keys[1]]) && $value[] = $this->_formatTime($model[$keys[1]]);
            }
            return $value;
        });
    }


    /**
     * @return $this
     */
    public function monthrange()
    {
        $this->type(__FUNCTION__);
        $this->setRangeResolve();
        return $this;
    }

    /**
     * 范围选择时选中日期所使用的当日内具体时刻
     *
     * @param array $times ['00:00:00', '23:59:59']
     * @return $this
     */
    public function defaultTime(array $times)
    {
        $this->attribute("default-time", $times);
        return $this;
    }

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
     * 选择范围时的分隔符
     *
     * @param string $separator
     * @return $this
     */
    public function rangeSeparator(string $separator = '-')
    {
//        echo $separator;
        $this->props('range-separator', $separator);
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
     * 表单提交格式 timestamp兼容性好，使用后端格式化
     *
     * @param string $format
     * @return $this
     */
    protected function _valueFormat(string $format = 'x')
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
     * 时间格式化
     *
     * @param string $format "YYYY-MM-DD HH-mm-ss"
     * @return $this
     */
    public function format(string $format = "YYYY-MM-DD HH-mm-ss")
    {
        $this->attribute('format', $format);
        return $this;
    }


    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function shortcuts(array $data)
    {
        foreach ($data as &$item) {
            if (!isset($item['text']) || !isset($item['start']) || !isset($item['end'])) {
                throw new Exception("Error in shortcuts parameter ");
            }
            $item['start'] = $this->_formatTime($item['start']);
            $item['end'] = $this->_formatTime($item['end']);
        }
        $this->pickerOptions = array_merge($this->pickerOptions, ["shortcuts" => $data]);
        return $this;
    }

    /**
     * 设置默认值
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        if (is_array($value) && count($value) === 2) {
            $value = [
                $this->_formatTime($value[0]),
                $this->_formatTime($value[1])
            ];
        } else {

            $value = $this->_formatTime($value);
        }

        $this->default = $value;
        return $this;
    }

    /**
     * 格式化时间
     * @param $value
     * @return float|int
     */
    protected function _formatTime($value)
    {
        if (is_numeric($value)) {
            $value = $value * 1000;
        } else {
            $value = empty($value) ? '' : strtotime($value) * 1000;
        }
        return $value;
    }



    /**
     * 固定值配置
     * @param $value
     * @param $default
     * @param $text
     * @return $this
     */
    public function fixedValue($value,$default,$text)
    {
        $this->props([
            'fixedValue' => $value,
            'fixedText' => $text,
            'fixedDefault' => $default,
        ]);
        return $this;
    }


    /**
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->attribute('picker-options', $this->pickerOptions);
        $this->type && $this->attribute('type', $this->type);
        return array_merge(parent::jsonSerialize(), []);
    }
}
