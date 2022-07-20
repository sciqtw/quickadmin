<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Field;
use quick\admin\form\fields\Radio;
use quick\admin\form\fields\Select;
use quick\admin\form\fields\Text;
use think\Collection;
use think\helper\Arr;

class Group extends FieldFilter
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
     * @var Select
     */
    public $selectField;


    protected $builder;


    /**
     * @var array
     */
    protected $group;


    /**
     * Group constructor.
     * @param $column
     * @param $label
     * @param \Closure $builder
     */
    public function __construct($column, $label, \Closure $builder)
    {


        $this->builder = $builder;
        $this->group = [];
        parent::__construct($column, $label);
    }

    public function condition($inputs)
    {
        $data = filter_params();
        $value = Arr::get($data, $this->requestColumn);
        $selectKey  = Arr::get($data, $this->selectColumn());

        if (empty($value)) {
            return false;
        }

        $this->value = $value;


        $this->group = [];
        call_user_func($this->builder, $this);
        $group = $this->group;
        $condition = isset($group[$selectKey]) ? $group[$selectKey]['condition']:false;
        if ($condition) {
            return $this->buildCondition(...$condition);
        }
    }

    protected function selectColumn()
    {
        return $this->requestColumn."_group";
    }

    public function init()
    {
        call_user_func($this->builder, $this);
        $options = [];
        foreach ($this->group as $k => $item){
            $options[$k] =  $item['label'];
        }
        $this->selectField = Select::make($this->selectColumn())->options($options)->hiddenLabel()->style('width', '100px');
    }

    public function getField()
    {
//        call_user_func($this->builder, $this);
        return $this->field->prepend($this->selectField);
    }


    /**
     * @param $label
     * @param array $condition
     * @return $this
     */
    protected function joinGroup($label, array $condition)
    {
        $this->group[] = compact('label', 'condition');
        return $this;
    }


    /**
     * Filter out `equal` records.
     *
     * @param string $label
     * @param string $operator
     *
     * @return Group
     */
    public function equal($label = '', $operator = '=')
    {
        $label = $label ?: $operator;
        $condition = [$this->column, $operator, $this->value];
        return $this->joinGroup($label, $condition);
    }


    /**
     * @param string $label
     * @return Group
     */
    public function notEqual($label = '')
    {
        return $this->equal($label, '!=');
    }


    /**
     * @param string $label
     * @return Group
     */
    public function gt($label = '')
    {
        return $this->equal($label, '>');
    }


    /**
     * @param string $label
     * @return Group
     */
    public function lt($label = '')
    {
        return $this->equal($label, '<');
    }


    /**
     * @param string $label
     * @return Group
     */
    public function nlt($label = '')
    {
        return $this->equal($label, '>=');
    }


    /**
     * @param string $label
     * @return Group
     */
    public function ngt($label = '')
    {
        return $this->equal($label, '<=');
    }


    /**
     * @param string $label
     * @param string $operator
     * @return $this
     */
    public function like($label = '', $operator = 'like')
    {
        $label = $label ?: $operator;
        $condition = [$this->column, $operator, "%{$this->value}%"];
        return $this->joinGroup($label, $condition);
    }


    /**
     * @param string $label
     * @param string $operator
     * @return $this
     */
    public function ilike($label = '', $operator = 'ilike')
    {
        $label = $label ?: $operator;
        $condition = [$this->column, $operator, "%{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }


    /**
     * @param string $label
     * @param string $operator
     * @return $this
     */
    public function startWith($label = '')
    {
        $label = $label ?: 'Start with';
        $condition = [$this->column, 'like', "{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }


    /**
     * @param string $label
     * @param string $operator
     * @return $this
     */
    public function endWith($label = '')
    {
        $label = $label ?: 'End with';
        $condition = [$this->column, 'like', "{$this->value}%"];

        return $this->joinGroup($label, $condition);
    }


    /**
     * @param $label
     * @param \Closure $builder
     * @return $this
     */
    public function where($label, \Closure $builder)
    {
        $this->input = $this->value;

        $condition = [$builder->bindTo($this)];

        return $this->joinGroup($label, $condition);
    }
}
