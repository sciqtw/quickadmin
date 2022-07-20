<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\components\Component;
use quick\admin\Element;
use quick\admin\form\Form;

class Text extends Field
{


    public $component = 'form-text-field';


    /**
     * 可清空
     *
     * @return $this
     */
    public function clearable()
    {
        $this->attribute(['clearable' => true]);
        return $this;
    }

    /**
     * 密码输入input
     *
     * @return $this
     */
    public function password()
    {
        $this->attribute(['show-password' => true]);
        return $this;
    }

    /**
     *
     * @param string $iconClass
     * @return $this
     */
    public function prefixIcon(string $iconClass)
    {
        $icon = Component::icon($iconClass);
        $this->children($icon,"prefix");
//        $this->attribute(['prefix-icon' => $iconClass]);
        return $this;
    }

    /**
     * @param string $iconClass
     * @return $this
     */
    public function suffixIcon(string $iconClass)
    {
        $icon = Component::icon($iconClass);
        $this->children($icon,"suffix");
//        $this->attribute(['suffix-icon' => $iconClass]);
        return $this;
    }


    /**
     * @param int $rows
     * @return $this
     */
    public function textarea(int $rows = 3)
    {
        $this->attribute([
            "type" => __FUNCTION__,
            "rows" => $rows,
        ]);
        return $this;
    }

    /**
     * textarea rows
     *
     * @param int $rows
     * @return $this
     */
    public function rows(int $rows)
    {
        $this->attribute(__FUNCTION__, $rows);
        return $this;
    }

    /**
     * @param int $minRows
     * @param int $maxRows
     * @return $this
     */
    public function autosize(int $minRows = 0, int $maxRows = 0)
    {
        $this->attribute([
            "type" => "textarea",
        ]);
        $autosize = [];
        $minRows && $autosize['minRows'] = $minRows;
        $maxRows && $autosize['maxRows'] = $maxRows;
        $this->attribute([
            "autosize" => $autosize,
        ]);

        return $this;
    }

    /**
     * @param null $min 最小值
     * @param null $max 最大值
     * @param null $step 步长
     * @return $this
     */
    public function number($min = null, $max = null, $step = null)
    {
        !is_null($min) && $this->attribute(["min" => $min]);
        !is_null($max) && $this->attribute(["max" => $max]);
        !is_null($step) && $this->attribute(["step" => $step]);
        $this->attribute([
            "type" => __FUNCTION__,
        ]);

        $this->setValueType('number');
        $this->props("type", __FUNCTION__);
        return $this;
    }


    /**
     * input 后置组件
     *
     * @param Element $component
     * @return $this
     */
    public function append(Element $component)
    {
        $component->slot("append");
        $this->children($component);
        return $this;
    }


    /**
     * input 前置组件
     *
     * @param Element $component
     * @return $this
     */
    public function prepend(Element $component)
    {
        $this->children($component, 'prepend');
        return $this;
    }


    /**
     *  input 前置
     *
     * @param string $text
     * @return $this
     */
    public function prependText(string $text)
    {
        $component = Component::custom("span")->children($text);
        $this->prepend($component);
        return $this;
    }

    /**
     * input 后置
     * @param string $text
     * @return $this
     */
    public function appendText(string $text)
    {
        $component = Component::custom("span")->children($text);
        $this->append($component);
        return $this;
    }

    /**
     * 设置为自动完成input
     * @param array $value
     * [ [ "value"=>"iphone4", ], [ "value"=>"iphone8", ] ]
     * @param string $key
     * @return $this
     */
    public function autocomplete(array $value, $key = 'value')
    {
        $this->attribute([
            'value-key' => $key,
            'debounce' => 400,
            'placement' => 'top-start',
        ]);
        $this->props('autocomplete', $value);
        return $this;
    }


    /**
     * @param string $name 字段
     * @param array $options 可选参数
     *  [ 3 => '选项1',5 => '选项2' ]
     * @param string $default 默认值
     * @param int $width 宽度
     * @return $this
     */
    public function select(string $name,array $options,string $default = '' ,int $width = 100)
    {

        $form = Form::make();
        $this->prepend($form->select($name, $name)->options($options)->labelWidth(0)->style([
            'width' => $width.'px',
        ])->hiddenLabel()->default($default));
        return $this;
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
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
