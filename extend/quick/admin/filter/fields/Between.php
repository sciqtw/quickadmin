<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\components\Component;
use quick\admin\form\fields\Field;
use quick\admin\form\fields\Text;
use quick\admin\form\fields\Date;
use quick\admin\form\fields\Time;
use think\helper\Arr;

class Between extends FieldFilter
{

    /**
     * @var Field
     */
    protected $starField;

    /**
     * @var Field
     */
    protected $endField;

    protected $fieldType;

    protected $startDefault;
    protected $endDefault;

    public function condition($inputs)
    {

        $startValue = Arr::get($inputs, $this->columnStartName());
        $endValue = Arr::get($inputs, $this->columnEndName());

        if (empty($startValue) || empty($endValue)) {
            return false;
        }


        $this->value = [$startValue,$endValue];

        return $this->buildCondition($this->column, 'between', $this->value);
    }

    private function columnStartName()
    {
        return $this->requestColumn.'_start';
    }

    private function columnEndName()
    {
        return $this->requestColumn.'_end';
    }


    public function defaultField()
    {

        $this->starField = Text::make($this->columnStartName(),$this->label);
        $this->endField = Text::make($this->columnEndName(),'-')->labelWidth(8);

        return $this;
    }


    /**
     * @param string $startDefault strart默认值
     * @param string $endDefault end 默认值
     * @return $this
     */
    public function date($startDefault = '',$endDefault = '')
    {
        !empty($startDefault) && $this->startDefault($startDefault);
        !empty($endDefault) && $this->endDefault($endDefault);
        $this->starField = Date::make($this->columnStartName(),$this->label);
        $this->endField = Date::make($this->columnEndName(),'-')->labelWidth(8);

        return $this;
    }


    /**
     * @param string $startDefault strart默认值
     * @param string $endDefault end 默认值
     * @return $this
     */
    public function datetime($startDefault = '',$endDefault = '')
    {
        !empty($startDefault) && $this->startDefault($startDefault);
        !empty($endDefault) && $this->endDefault($endDefault);
        $this->starField = Date::make($this->columnStartName(),$this->label)->datetime();
        $this->endField = Date::make($this->columnEndName(),'-')->datetime()->labelWidth(8);

        return $this;
    }

    /**
     * @param string $startDefault strart默认值
     * @param string $endDefault end 默认值
     * @return $this
     */
    public function time($startDefault = '',$endDefault = '')
    {
        !empty($startDefault) && $this->startDefault($startDefault);
        !empty($endDefault) && $this->endDefault($endDefault);
        $this->starField = Time::make($this->columnStartName(),$this->label);
        $this->endField = Time::make($this->columnEndName(),'-')->labelWidth(8);

        return $this;
    }


    public function getField()
    {
        $formItem = Component::custom('div')->style('display','flex')->children([
            $this->starField->default($this->startDefault ?? ''),
            $this->endField->default($this->endDefault ?? '')
        ]);
        return $formItem;
    }


    /**
     * 设置start默认值
     * @param $value
     */
    public function startDefault($value)
    {
        $this->startDefault = $value;
    }

    /**
     * 设置end默认值
     * @param $value
     */
    public function endDefault($value)
    {
        $this->endDefault = $value;
    }
}