<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class Lt extends FieldFilter
{


    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->requestColumn);

        if (is_null($value) || empty($value)) {
            return false;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, '<', $this->value);
    }

}