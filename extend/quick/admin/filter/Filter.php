<?php
declare (strict_types=1);

namespace quick\admin\filter;


use quick\admin\components\Component;
use quick\admin\filter\fields\Between;
use quick\admin\filter\fields\Date;
use quick\admin\filter\fields\Day;
use quick\admin\filter\fields\EndsWith;
use quick\admin\filter\fields\Equal;
use quick\admin\filter\fields\FieldFilter;
use quick\admin\filter\fields\Group;
use quick\admin\filter\fields\Gt;
use quick\admin\filter\fields\In;
use quick\admin\filter\fields\Like;
use quick\admin\filter\fields\Lt;
use quick\admin\filter\fields\Month;
use quick\admin\filter\fields\Ngt;
use quick\admin\filter\fields\Nlt;
use quick\admin\filter\fields\NotEqual;
use quick\admin\filter\fields\NotIn;
use quick\admin\filter\fields\StartsWith;
use quick\admin\filter\fields\Week;
use quick\admin\filter\fields\Where;
use quick\admin\filter\fields\Year;
use quick\admin\form\fields\FooterField;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;
use quick\admin\table\Query;
use quick\admin\Element;
use think\Collection;
use think\Exception;
use think\helper\Arr;
use think\Model;

/**
 * Class Filter
 * @package quick\filter
 * @method Equal equal($column, $label = '')
 * @method NotEqual notEqual($column, $label = '')
 * @method Like like($column, $label = '')
 * @method EndsWith endsWith($column, $label = '')
 * @method StartsWith startsWith($column, $label = '')
 * @method Gt gt($column, $label = '')
 * @method Lt lt($column, $label = '')
 * @method Ngt ngt($column, $label = '')
 * @method Nlt nlt($column, $label = '')
 * @method Between between($column, $label = '')
 * @method In in($column, $label = '')
 * @method NotIn notIn($column, $label = '')
 * @method Day day($column, $label = '')
 * @method Week week($column, $label = '')
 * @method Month month($column, $label = '')
 * @method Year year($column, $label = '')
 * @method Date date($column, $label = '')
 * @method Where where($callback, $label = '',$column)
 * @method Group group($column, $label, \Closure $builder)
 */
class Filter extends Element
{
    /**
     * @var Query|Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var array
     */
    protected static $supports = [
        'equal' => Equal::class,
        'notEqual' => NotEqual::class,
        'like' => Like::class,
        'endsWith' => EndsWith::class,
        'startsWith' => StartsWith::class,
        'gt' => Gt::class,
        'lt' => Lt::class,
        'ngt' => Ngt::class,
        'nlt' => Nlt::class,
        'between' => Between::class,
        'in' => In::class,
        'notIn' => NotIn::class,
        'day' => Day::class,
        'week' => Week::class,
        'month' => Month::class,
        'year' => Year::class,
        'date' => Date::class,
        'where' => Where::class,
        'group' => Group::class,
    ];

    /**
     * fieldWidth
     * @var
     */
    protected $fieldWidth;

    /**
     * @var
     */
    protected $form;


    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var bool
     */
    public $expand = false;

    /**
     * @var Collection
     */
    protected $scopes;

    protected $gutter = 15;


    /**
     * Create a new filter instance.
     *
     * @param Model $model
     */
    public function __construct(Query $model = null)
    {
        $this->model = $model;
        $this->scopes = new Collection();
        $this->form = Form::make("filter");
    }


    /**
     * Get table model.
     *
     * @return Model
     */
    public function getModel()
    {
        $conditions = array_merge(
            $this->conditions(),
            $this->scopeConditions()
        );

        return $this->model->addConditions($conditions);
    }


    /**
     * 展开过滤器容器。
     *
     * @return $this
     */
    public function expand()
    {
        $this->expand = true;
        return $this;
    }


    /**
     * 创建查询条件
     *
     * @return mixed
     */
    public function conditions()
    {
        $inputs = Arr::dot(filter_params());
        $inputs = array_filter($inputs, function ($input) {
            return $input !== '' && !is_null($input);
        });
        $conditions = [];
        $relations = [];
        /** @var FieldFilter $filter */
        foreach ($this->getFilters() as $filter) {
            $condition = $filter->condition($inputs);
            if(isset($condition['hasWhere'])){
                $relations[$condition['hasWhere']['relation']][] = $condition['hasWhere'];
               continue;
            }
            $conditions[] = $condition;
        }

        if(!empty($relations)){
            foreach($relations as $key => $rela){
                $conditions[] = ['hasWhere' => [$key, function ($relation) use ($rela) {
                    foreach ($rela as $item){
                        call_user_func_array([$relation, $item['query']], $item['args']);
                    }
                }]];
            }

        }
//        dump($relations);
//        dump($conditions);die;

//        return ['hasWhere' => [$relation, function ($relation) use ($args) {
//            call_user_func_array([$relation, $this->query], $args);
//        }]];

        return array_filter($conditions);
    }


    /**
     * 添加一个过滤器
     * @param FieldFilter $filter
     * @return FieldFilter
     */
    protected function addFilter(FieldFilter $filter)
    {

        return $this->filters[] = $filter;
    }


    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }


    /**
     * @param bool $toArray
     * @return Collection
     * @throws \Exception
     */
    public function execute($toArray = true)
    {
        if (method_exists($this->model->eloquent(), 'paginate')) {
            $this->model->usePaginate(true);

            return $this->model->buildData($toArray);
        }
        $conditions = $this->conditions();

        return $this->model->addConditions($conditions)->buildData($toArray);
    }


    /**
     * 扩展filter
     *
     * @param $name
     * @param $filterClass
     * @throws Exception
     */
    public static function extend($name, $filterClass)
    {
        if (!is_subclass_of($filterClass, FieldFilter::class)) {
            throw new Exception("The class [$filterClass] must be a type of " . FieldFilter::class . '.');
        }

        static::$supports[$name] = $filterClass;
    }


    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->fieldWidth = $width;
        return $this;
    }


    /**
     * 设置form label width
     *
     * @param int $width
     * @return $this
     */
    public function labelWidth(int $width)
    {
        $this->getForm()->attribute(["label-width" => $width . "px"]);
        return $this;
    }


    /**
     * @return mixed
     */
    protected function getWidth()
    {
        return $this->fieldWidth ?? 8;
    }

    /**
     * @return Form
     */
    protected function getForm()
    {
        return $this->form;
    }


    /**
     * @return Form
     */
    public function resolveForm()
    {

        [$width, $filters, $filterData] = [$this->getWidth(), $this->filters, filter_params()];
        $this->getForm()->row(function (Row $row) use ($width, $filters) {
            $row->attribute("gutter", $this->gutter)->props('justify','start');
            /** @var FieldFilter $filter */
            foreach ($filters as $filter) {
                $filterWidth = $filter->getWidth();
                $row->col((empty($filterWidth) ? $width : $filterWidth), $filter->getField());
            }
            $row->col([
                "xs" => 24,
                "sm" => 24,
                "md" => 12,
                "lg" => 8,
                "xl" => 8,
            ],FooterField::make()->hideCancel()->submitBtn(Component::button('查询','primary')->props('icon','el-icon-search')));
        });

        $this->getForm()->labelPosition('left');


        $conditions = $this->conditions();
        if (!empty($conditions)) {
            $this->expand();//存在查询条件默认展开过滤器
        }
        $this->getForm()->resolve($filterData);
        return $this->getForm();
    }


    /**
     * @param $abstract
     * @param $arguments
     * @return mixed
     */
    public function findFilter($abstract, $arguments)
    {
        if (isset(static::$supports[$abstract])) {
            return new static::$supports[$abstract](...$arguments);
        }
    }

    /**
     * @param $method
     * @param $arguments
     * @return $this|FieldFilter
     */
    public function __call($method, $arguments)
    {
        if ($filter = $this->findFilter($method, $arguments)) {
            return $this->addFilter($filter);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'form' => $this->resolveForm()
        ]);
    }
}
