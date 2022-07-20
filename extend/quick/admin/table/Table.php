<?php


declare (strict_types=1);

namespace quick\admin\table;


use quick\admin\table\traits\BuildTableDataTrait;
use quick\admin\table\traits\HandleActionColumnTrait;
use quick\admin\table\traits\HandleTablePropsTrait;
use quick\admin\Element;
use think\db\BaseQuery;
use think\helper\Str;
use think\Model;
use think\model\Relation;
use think\model\relation\BelongsTo;
use think\model\relation\BelongsToMany;
use think\model\relation\HasMany;
use think\model\relation\HasManyThrough;
use think\model\relation\HasOne;
use think\model\relation\MorphOne;
use think\model\relation\MorphToMany;


/**
 * Class Table
 * @package quick\table
 */
class Table extends Element
{

    use BuildTableDataTrait,
        HandleActionColumnTrait,
        HandleTablePropsTrait;

    /**
     * 表单标题
     */
    protected $title;

    /**
     * columns
     *
     * @var array
     */
    protected $columns = [];

    /**
     * @var
     */
    protected $model;

    /**
     * 表数据
     * @var
     */
    protected $rows;

    /**
     * @var array
     */
    protected $perPages = [10, 15, 20, 30, 50, 100];

    /**
     * @var int
     */
    protected $perPage = 20;

    /**
     * vue component
     *
     * @var string
     */
    public $component = "quick-table";

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var \think\Request
     */
    protected $request;

    /**
     * @var bool|Paginate
     */
    protected $paginate;

    /**
     * 是否使用分页
     *
     * @var bool
     */
    protected $usePaginate = true;

    /**
     * 默认主键字段
     *
     * @var string
     */
    protected $keyName = 'id';


    protected $loadUrl;

    /**
     * @var bool
     */
    protected $disableFilter = false;

    /**
     * 禁止操作行
     *
     * @var bool
     */
    protected $disableActions = false;

    /**
     * 默认隐藏column
     *
     * @var array
     */
    protected $hideColumns = [];

    /**
     * Table constructor.
     * @param array $model
     */
    public function __construct($model = [])
    {
        if (is_array($model)) {
            $this->rows = $model;
        } elseif ($model instanceof Query) {
            $this->query = $model;
        } else {
            $this->model = $model;
        }

        $this->request = app()->request;
        $this->initialize();
        $this->callInitCallbacks();
    }


    /**
     *
     */
    protected function initialize()
    {
        $this->defaultSize('mini');
        $this->columns = [];

    }


    public function getKey()
    {
        return $this->keyName;
    }



    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->rows = $data;
        return $this;
    }


    /**
     * 获取主键
     *
     * @return string
     */
    public function getKeyName()
    {
        return $this->keyName ?: 'id';
    }


    /**
     * @param bool $expandAll 默认全部展开
     * @param string $key row-key
     * @param string $childrenKey tree-props:children
     * @return $this
     */
    public function tree(bool $expandAll = true,string $key = 'id.value', string $childrenKey = '_children')
    {
        $this->props([
            'row-key' => $key,
            'default-expand-all' => $expandAll,
            'tree-props' => [
                'children' => $childrenKey
            ]
        ]);
        return $this;
    }


    /**
     * 禁用过滤器
     *
     * @return $this
     */
    public function disableFilter()
    {
        $this->disableFilter = true;
        return $this;
    }


    /**
     * 禁止操作
     *
     * @return $this
     */
    public function disableActions()
    {
        $this->disableActions = true;
        return $this;
    }


    /**
     * 每页数据数量
     *
     * @param int $size
     * @return $this
     */
    public function paginate(int $size)
    {
        $this->perPage = $size;
        return $this;
    }


    /**
     * 每页显示个数选择器的选项设置
     *
     * @param array $perPages
     * @return $this
     */
    public function perPages(array $perPages)
    {
        $this->perPages = $perPages;
        return $this;
    }


    /**
     * 设置分页
     * @param $total
     * @param $currentPage
     * @return $this
     */
    protected function setPaginate($total, $currentPage)
    {

        if (!$this->paginate) {
            $per_page = request()->param('per_page/d');
            !$per_page && $per_page = $this->perPage;
            $paginate = Paginate::make($total, $currentPage);
            $paginate->pageSizes($this->perPages);
            $paginate->pageSize((int)$per_page);
            $this->paginate = $paginate;
        }
        return $this;
    }


    /**
     * @return bool|Paginate
     */
    protected function getPaginate()
    {
        if (!$this->usePaginate) {
            return false;
        }

        if (!$this->paginate) {
            $this->setPaginate(1, 1);
        }
        return $this->paginate;
    }


    /**
     *  默认隐藏column
     *
     * @param array $columnName
     * @return $this
     */
    public function hideColumns(array $columnName)
    {
        $this->hideColumns = array_merge($this->hideColumns, (array)$columnName);
        return $this;
    }


    /**
     * 是否带有纵向边框
     *
     * @param bool $border
     * @return $this
     */
    public function border($border = true)
    {
        $this->attribute("border", $border);
        return $this;
    }

    /**
     *  获取数据model
     *
     * @return BaseQuery|Model
     */
    public function getModel()
    {
        $model = $this->model;
        if (!$model) {
            $model = $this->getQuery()->eloquent();
        }
        return $model;
    }


    /**
     * get columns of table
     *
     * @return array
     */
    public function getVisibleColumns()
    {
        $columns = $this->columns;
        // 行操作
        if (!$this->disableActions) {
            $columns = array_merge($columns, [$this->getActionColumn()]);
        }
        return $this->filterVisibleColumns($columns);
    }


    /**
     * 过滤可显示column
     *
     * @param array $columns
     * @return array
     */
    protected function filterVisibleColumns(array $columns)
    {

        return collect($columns)->filter(function ($column) {
            if ($column instanceof Column) {
                if ($children = $column->getChildren()) {
                    return $column->setChildren($this->filterVisibleColumns((array)$children));
                }
                return $column->canDisplayColumn($this->request);
            } else {
                return true;
            }

        })->toArray();
    }


    /**
     * append column to table
     *
     * @param $column
     * @return $this
     * @throws \Exception
     */
    protected function appendColumn($column)
    {

        if (is_callable($column)) {
            call_user_func($column, $this);
        } else {
            $column = is_array($column) ? $column : [$column];
            $this->columns = array_merge($this->columns, $column);
        }

        return $this;
    }


    /**
     *  add column for table
     *
     * @param  $name
     * @param  $label
     * @return $this|Column
     * @throws \Exception
     */
    public function column($name, $label = '')
    {
        if (is_string($name)) {

            if (Str::contains($name, '.')) {
                return $this->addRelationColumn($name, $label);
            }

            if (Str::contains($name, '->')) {
                return $this->addJsonColumn($name, $label);
            }
        }
        if($name instanceof \Closure){
            return $this->addColumn($name, $label);
        }

        if ($column = $this->handleRelationColumn($name, $label)) {
            return $column;
        }

        return $this->addColumn($name, $label);
    }


    /**
     * 批量添加column
     *
     * @param array $columns
     * @return $this
     * @throws \Exception
     */
    public function columns($columns = [])
    {
        if (func_num_args() == 1 && is_array($columns)) {
            foreach ($columns as $column => $label) {
                $this->column($column, $label);
            }

        } else {
            foreach (func_get_args() as $column) {
                $column = explode("|", $column);
                $this->column($column[0], $column[1] ?? $column[0]);
            }
        }

        return $this;
    }


    /**
     *
     * @param string $column
     * @param string $label
     * @return Column
     * @throws \Exception
     */
    protected function addColumn($column = '', $label = '')
    {
        $element = new Column($column, $label);
        $element->setTable($this);
        $this->appendColumn($element);
        return $element;
    }


    /**
     * @param $name
     * @param string $label
     * @return $this|Column
     * @throws \Exception
     */
    protected function addRelationColumn($name, $label = '')
    {
        list($relation, $column) = explode('.', $name);

        $model = $this->getQuery()->eloquent();
        if (!method_exists($model, $relation) || !$model->{$relation}() instanceof Relation) {

            $class = get_class($model);
            abort(500, "Call to undefined relationship [{$relation}] on model [{$class}].");

            return $this;
        }

        $name = Str::snake($relation.'_'.$column);

        $this->getQuery()->setWith($relation);
//        $model = $model->with($relation);//关联预载入
//        $this->getQuery()->setModel($model);

        return $this->addColumn($name, $label)->setRelation($relation, $column);
    }


    /**
     * @param $method
     * @param $label
     * @return bool|Column
     * @throws \Exception
     */
    protected function handleRelationColumn($method, $label)
    {
        $model = $this->getQuery()->eloquent();

        if (!method_exists($model, $method)) {
            return false;
        }

        try {
            if (!($relation = $model->$method()) instanceof Relation) {
                return false;
            }
        }catch (\Exception |\Throwable $e){
            return false;
        }


        if ($relation instanceof HasOne ||
            $relation instanceof BelongsTo ||
            $relation instanceof MorphOne
        ) {

            $model = $model->with($method);
            $this->getQuery()->setModel($model);

            return $this->addColumn($method, $label)->setRelation($method);
        }

        if ($relation instanceof HasMany
            || $relation instanceof BelongsToMany
            || $relation instanceof MorphToMany
            || $relation instanceof HasManyThrough
        ) {

            $model = $model->with($method);
            $this->getQuery()->setModel($model);

            return $this->addColumn($method, $label);
        }

        return false;
    }

    /**
     * @param $name
     * @param string $label
     * @return Column
     * @throws \Exception
     */
    protected function addJsonColumn($name, $label = '')
    {

        $column = substr($name, strrpos($name, '->') + 2);
        $name = str_replace('->', '.', $name);

        return $this->addColumn($name, $label ?: ucfirst($column));
    }


    /**
     * 数据加载地址
     *
     * @param string $url
     * @return $this
     */
    public function loadUrl(string $url)
    {
        $this->loadUrl = $url;
        return $this;
    }


    /**
     * @return mixed
     */
    protected function getLoadUrl()
    {
        return $this->loadUrl;
    }


    /**
     * @return TablePanel
     */
    public function displayPanel()
    {

        return TablePanel::make();
    }


    /**
     * 获取当前类的子类
     *
     * @return array
     */
    public function getChildrenComponents(): array
    {
        return array_merge(
            $this->getVisibleColumns(),
            $this->getBatchActions()->getActions(),
            $this->getTableTools()->getActions()
        );
    }


    /**
     * @return TablePanel
     * @throws \Exception
     */
    public function displayRender()
    {
        $panel = $this->displayPanel();
        $panel->props([
            "tableData" => $this->jsonSerialize(),
            'showFilter' => !$this->disableFilter,
            'filter' => $this->getFilter(),
            'loadUrl' => $this->getLoadUrl(),
        ]);
        if(is_array($this->children) && !empty($this->children)){
            $panel->children($this->children);
//            /** @var Element $child */
//            foreach ($this->children as $child){
//                if($child instanceof Element){
//                    if($child->getSlot() == 'header'){
//                        $panel->children($child);
//                    }
//                }
//            }
        }

        return $panel;
    }


    /**
     * @param array $config
     * @param Table|null $table
     * @return Table|null
     */
    public static function buildTable(array $config,?Table $table = null)
    {

//        $list = [
//            [
//                'type' => 'column',
//                'args' => [
//                    'name',
//                    '名称',
//                ],
//                'methods' => [
//                    [
//                        'type' => 'width',
//                        'args' => [ '100']
//                    ]
//                ]
//
//            ]
//        ];

        if(!$table){
            $table = self::make();
        }
        foreach ($config as $field) {
            if (is_callable([$table, $field['type']])) {
                $formItem = $table->{$field['type']}(...$field['args']);
                foreach ($field['methods'] as $item) {
                    if (is_callable([$formItem, $item['type']])) {
                        $formItem->{$item['type']}(...$item['args']);
                    }
                }
            }

        }
        return $table;

    }

    public function setComponent($component)
    {
        $this->component = $component;
        return $this;
    }


    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function jsonSerialize()
    {
        $batchActions = $this->getBatchActions();
        if (count($batchActions->getActions()) > 0) {
            $this->showSelection();
        }

        $lists = $this->buildData();
        $paginate = $this->getPaginate();

        $this->props([
            'columns' => $this->getVisibleColumns(),
            'visibleColumns' => $this->visibleColumnKeys(),//默认显示column
            'paginate' => $this->getPaginate(),
            "batchActions" => $batchActions,
            "tableTools" => $this->getTableTools(),
            "showPaginate" => $this->usePaginate,
            'maxLength' => 3,
            'total' => $paginate ? $paginate->total : 0,
            'currentPage' => $paginate ? $paginate->currentPage : 1,
            'pageSize' => $paginate ? $paginate->perPage : 15,
            'lists' => $lists['data'],
        ]);
        return array_merge(parent::jsonSerialize(), []);
    }

}
