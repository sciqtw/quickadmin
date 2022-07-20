<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class Where extends FieldFilter
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
     * Where constructor.
     * @param \Closure $query
     * @param $label
     * @param null $column
     * @throws \ReflectionException
     */
    public function __construct(\Closure $query, $label, $column = null)
    {

        $this->where = $query;

        $requestColumn = $column;
        if (stripos($column, $this->alias) !== false) {
            list($column, $requestColumn) = explode($this->alias, $column);
        }

        $this->column = $column ?: static::getQueryHash($query, $this->label);
        $this->requestColumn = $requestColumn;
        $this->label = $this->formatLabel($label);

        $this->defaultField();
        $this->init();
    }

    /**
     * @param \Closure $closure
     * @param string $label
     * @return string
     * @throws \ReflectionException
     */
    public static function getQueryHash(\Closure $closure, $label = '')
    {
        $reflection = new \ReflectionFunction($closure);

        return md5($reflection->getFileName().$reflection->getStartLine().$reflection->getEndLine().$label);
    }


    /**
     * @param array $inputs
     * @return array|mixed|void|null
     * @throws \ReflectionException
     */
    public function condition($inputs)
    {
        $value = Arr::get($inputs, $this->requestColumn ?: static::getQueryHash($this->where, $this->label));

        if (is_null($value)) {
            return;
        }

        $this->input = $this->value = $value;

        return $this->buildCondition($this->where->bindTo($this));
    }
}
