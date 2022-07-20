<?php
declare (strict_types = 1);
/**
 *
 */

namespace quick\admin\filter\fields;


use quick\admin\form\fields\Date;
use quick\admin\form\fields\Field;
use quick\admin\form\fields\Text;
use think\helper\Arr;

/**
 * Class FieldFilter
 * @package quick\filter\fields
 */
abstract class FieldFilter
{


    /**
     * 名称
     *
     * @var string
     */
    protected $label;


    /**
     * span 宽度 n/24
     *
     * @var
     */
    protected $width;

    /**
     * 当前值
     * @var
     */
    protected $value;

    /**
     * 显示默认值
     *
     * @var array|string
     */
    protected $defaultValue;

    /**
     * 字段名称
     * @var string
     */
    protected $column;

    /**
     * 请求变量名称
     *
     * @var
     */
    protected $requestColumn;

    /**
     * @var string
     */
    protected $alias = "#";

    /**
     * @var
     */
    protected $field;

    /**
     * 查询条件
     *
     * @var string
     */
    protected $query = 'where';

    /**
     * @var bool
     */
    protected $ignore = false;


    /**
     * AbstractFilter constructor.
     *
     * @param $column
     * @param string $label
     */
    public function __construct($column, $label = '')
    {
        $requestColumn = $column;
        if (stripos($column, $this->alias) !== false) {
            list($column, $requestColumn) = explode($this->alias, $column);
        }

        $this->column = $column;
        $this->requestColumn = $requestColumn;
        $this->label = $this->formatLabel($label);
        $this->defaultField();
        $this->init();
    }



    /**
     * init
     */
    public function init()
    {

    }


    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->width = $width;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }


    /**
     * 设置默认表单字段
     *
     * @return Field
     */
    protected function defaultField()
    {
        return $this->setField(Text::make($this->requestColumn,$this->label));
    }


    /**
     * @param Field $field
     * @return Field
     */
    public function setField(Field $field)
    {
        $this->field = $field;
        return $this->field;
    }


    /**
     * @return Field
     */
    public function getField()
    {
        if($this->field instanceof  Field){
            $this->field->default($this->defaultValue);
        }
        return $this->field;
    }


    /**
     * @param $label
     * @return string|string[]
     */
    protected function formatLabel($label)
    {
        $label = $label ?: ucfirst($this->requestColumn);

        return str_replace(['.', '_'], ' ', $label);
    }


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

        return $this->buildCondition($this->column, $this->value);
    }


    /**
     * Build conditions of filter.
     *
     * @return mixed
     */
    protected function buildCondition()
    {
        $column = explode('.', $this->column);
        return [$this->query => func_get_args()];
//        if (count($column) == 1) {
//            return [$this->query => func_get_args()];
//        }
//
//        return $this->buildRelationQuery(...func_get_args());
    }

    /**
     * 关联查询
     * @return array
     */
    protected function buildRelationQuery()
    {
        $args = func_get_args();

        $relation = substr($this->column, 0, strrpos($this->column, '.'));
        $data = explode('.', $this->column);
        $args[0] = end($data);

//        return ['hasWhere' => [$relation, function ($relation) use ($args) {
//            call_user_func_array([$relation, $this->query], $args);
//        }]];
        return ['hasWhere' => [
            'relation' => $relation,
            'query' =>  $this->query,
            'args' => $args,
        ]];
    }

    /**
     * 禁止此过滤条件
     *
     * @return $this
     */
    public function ignore()
    {
        $this->ignore = true;

        return $this;
    }


    /**
     * Set default value for filter.
     *
     * @param null $default
     *
     * @return $this
     */
    public function default($default = null)
    {
        if ($default) {
            $this->defaultValue = $default;
        }
        return $this;
    }


    /**
     * @return string
     */
    public function getColumn()
    {

        return $this->column;
    }

    /**
     * Get value of current filter.
     *
     * @return array|string
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * @param $method
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $params)
    {
        if (method_exists($this->getField(), $method)) {
            return $this->getField()->{$method}(...$params);
        }

        throw new \Exception('Method "' . $method . '" not exists.');
    }
}
