<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class NotEqual extends FieldFilter
{


    /**
     * Get query condition from filter.
     *
     * @param array $inputs
     *
     * @return array|mixed|null
     */
    public function condition($inputs)
    {
        if ($this->ignore) {
            return;
        }
        $value = Arr::get($inputs, $this->requestColumn);

        if (!isset($value)) {
            return;
        }

        $this->value = $value;

        return $this->buildCondition($this->column, "<>",$this->value);
    }


}