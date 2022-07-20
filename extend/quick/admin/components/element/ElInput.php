<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\Custom;
use quick\admin\Element;

class ElInput extends Element
{


    public $component = 'el-input';


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
        $this->attribute(['prefix-icon' => $iconClass]);
        return $this;
    }

    /**
     * @param string $iconClass
     * @return $this
     */
    public function suffixIcon(string $iconClass)
    {
        $this->attribute(['suffix-icon' => $iconClass]);
        return $this;
    }


    /**
     * @param $rows
     * @return $this
     */
    public function textarea(string $rows = '3')
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
     * @param null $min
     * @param null $max
     * @param null $step
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
        // todo attr props 重复
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
        $component->slot("prepend");
        $this->children($component);
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
        $component = Custom::make("span")->children($text);
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
        $component = Custom::make("span")->children($text);
        $this->append($component);
        return $this;
    }

    /**
     * 设置为自动完成input
     * @param $value
     * @param string $key
     * @return $this
     */
    public function autocomplete($value, $key = 'title')
    {
        $this->attribute([
            'value-key' => $key,
            'debounce' => 400,
            'placement' => 'top-start',
        ]);
        $this->attribute('autocomplete', $value);
        return $this;
    }
}
