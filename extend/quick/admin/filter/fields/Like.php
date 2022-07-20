<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class Like extends FieldFilter
{

    /**
     * @var string
     */
    protected $exprFormat = '%{value}%';

    /**
     * @var string
     */
    protected $operator = 'like';

    /**
     * @param array $inputs
     * @return array|mixed|void|null
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->requestColumn);

        if (is_array($value)) {
            $value = array_filter($value);
        }

        if (is_null($value) || empty($value)) {
            return;
        }

        $this->value = $value;

        $expr = str_replace('{value}', $this->value, $this->exprFormat);

        return $this->buildCondition($this->column, $this->operator, $expr);
    }


}