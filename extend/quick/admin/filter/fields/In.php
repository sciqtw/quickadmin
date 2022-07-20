<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Checkbox;
use quick\admin\form\fields\Select;
use think\helper\Arr;

class In extends FieldFilter
{

    protected $query = 'whereIn';

    public function condition($inputs)
    {
        $data = filter_params();
        $value = Arr::get($data, $this->requestColumn);

        if (empty($value)) {
            return false;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, $this->value);
    }


    /**
     * 多选
     * @param array $options 可选参数
     * [ 'key' => 'value']
     * @return $this
     */
    public function multipleSelect(array $options)
    {
        $this->field = Select::make($this->requestColumn,$this->label)
            ->multiple()
            ->options($options);
        return $this;
    }


    /**
     * @param array $options
     * @return Checkbox
     */
    public function checkbox(array $options)
    {
        $this->field = Checkbox::make($this->requestColumn,$this->label)->options($options)->checkButton();
        return $this->field;
    }

}
