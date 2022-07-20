<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Field;
use think\helper\Arr;

class Month extends Day
{

    protected $query = 'whereMonth';


    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->requestColumn);

        if (empty($value)) {
            return false;
        }

        if(is_numeric($value)){
            $value = strlen((string)$value) > 11 ? $value/1000:$value;
            $value = date('Y-m',$value);
        }

        $this->value = $value;


        return $this->buildCondition($this->column, $this->value);
    }

    /**
     * 设置默认表单字段
     *
     * @return Field
     */
    protected function defaultField()
    {
        return $this->setField(\quick\admin\form\fields\Date::make($this->requestColumn,$this->label)->month());
    }

}