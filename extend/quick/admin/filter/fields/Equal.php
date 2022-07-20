<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Field;
use quick\admin\form\fields\Radio;
use quick\admin\form\fields\Select;
use quick\admin\form\fields\Text;
use think\helper\Arr;

class Equal extends FieldFilter
{
    /**
     * Query closure.
     *
     * @var \Closure
     */
    protected $where;

    /**
     * Input value from presenter.
     *
     * @var mixed
     */
    public $input;


    /**
     * @param array $options
     * @return Radio
     */
    public function radio(array $options)
    {
        $this->field = Radio::make($this->requestColumn, $this->label)->options($options)->radioButton();
        return $this->field;
    }

    /**
     * @param array $options
     * @param string $key
     * @param string $label
     * @return Select
     */
    public function select(array $options, $key = '', string $label = '')
    {
        $this->field = Select::make($this->requestColumn, $this->label)->options($options, $key, $label);
        return $this->field;
    }


}
