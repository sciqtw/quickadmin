<?php
declare (strict_types=1);

namespace quick\admin\table\traits;


use quick\admin\filter\Filter;
use quick\admin\table\Query;
use quick\admin\table\Column;
use SebastianBergmann\CodeCoverage\TestFixture\C;
use think\paginator\driver\Bootstrap;

trait BuildTableDataTrait
{


    /**
     * @var Query
     */
    public $query;

    /**
     * @var string
     */
    public static $searchKey = 'search';


    /**
     *  排序字段参数key
     * @var string
     */
    public static $_sortKey = "_sort";

    /**
     *
     * @var string
     */
    public static $_orderKey = "_order";



    /**
     * 快捷搜索
     *
     * @var array|string|\Closure
     */
    protected $search;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var \Closure
     */
    protected $dataCallback;


    /**
     * 获取查询对象
     *
     * @return Query
     */
    public function getQuery()
    {
        if (!($this->query instanceof Query)) {
            $query = new Query($this->model);
            if (!$this->usePaginate) {
                $query->disablePagination();
            }
            $this->query = $query;
        }
        return $this->query;
    }


    /**
     *
     * 禁用分页
     *
     * @return $this
     */
    public function disablePagination()
    {
        $this->usePaginate = false;
        return $this;
    }


    /**
     * 设置快捷搜索
     *
     * @param array|string|\Closure $search
     * @return $this
     */
    public function search($search = null)
    {
        if (func_num_args() > 1) {
            $this->search = func_get_args();
        } else {
            $this->search = $search;
        }
        return $this;
    }


    /**
     * @param Filter $filter
     * @return $this
     */
    public function setFilter(Filter $filter)
    {
        $this->filter = $filter;
        return $this;
    }


    /**
     * @param \Closure $func
     * @return $this
     */
    public function filter(\Closure $func)
    {
        $filter = Filter::make();
        call_user_func($func,$filter);
        $this->setFilter($filter);
        return $this;
    }


    /**
     * @return Filter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * 处理快捷搜索
     *
     * @return $this
     */
    public function handleQuickSearch()
    {
        $value = request()->param(static::$searchKey);
        if (!$value || empty($this->search)) {
            return $this;
        }

        if (is_array($this->search)) {
            $columns = implode("|", $this->search);
        } else if (is_string($this->search)) {
            $columns = $this->search;
        }

        !empty($columns) && $this->getQuery()->where($columns, "like", "%{$value}%");

        return $this;
    }


    /**
     * 处理过滤器
     *
     */
    public function handleFilter()
    {
        $filter = $this->getFilter();
        if ($filter instanceof Filter) {
            $conditions = $filter->conditions();
            $this->getQuery()->addConditions($conditions);
        }

    }

    /**
     * 处理排序
     *
     */
    public function handleSort()
    {
        $order = $this->request->param(self::$_orderKey);
        $sort = $this->request->param(self::$_sortKey);
        if (!empty($order) && !empty($sort)) {

            $this->props(['default-sort' => [
                'prop' => $sort,
                'order' => $order,
            ]]);

            $order = $order == 'descending' ? 'asc' :'desc' ;
            $this->getQuery()->order($sort, $order);
        }
    }


    /**
     * 设置数据回调
     *
     * @param \Closure $dataCallback
     * @return $this
     */
    public function dataUsing(\Closure $dataCallback)
    {
        $this->dataCallback = $dataCallback;
        return $this;
    }


    /**
     * @return array|\think\Collection
     * @throws \Exception
     */
    public function handleQuery()
    {
        if (!empty($this->rows)) {
            return $this->rows;
        }
        if (empty($this->getModel())) {
            return [];
        }

        !$this->usePaginate && $this->getQuery()->disablePagination();
        $this->handleQuickSearch();
        $this->handleFilter();
        $this->handleSort();
        $data = $this->getQuery()->paginate($this->perPage)->buildData();


        if ($data instanceof Bootstrap) {
            $total = $data->total();
            $currentPage = $data->currentPage();
            $data = $data->getCollection();
        } else {

            $total = isset($data['total']) ? $data['total'] : count($data);
            $currentPage = isset($data['current_page']) ? $data['current_page'] : 1;
        }

        if ($this->usePaginate) {
            $this->setPaginate($total, $currentPage);
        }


        // 数据回调
        if ($this->dataCallback) {
            $data = call_user_func($this->dataCallback, $data);
        }
        $this->rows = $data;

        return $this->rows;
    }

    /**
     * columns 数组树展开
     *
     * @param array $columns
     * @return array
     */
    protected function flattenColumns(array $columns)
    {
        $list = [];
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                if ($children = $column->getChildren()) {
                    $children = $this->flattenColumns((array)$children);
                }
                if ($children) {
                    //存在子类的column不显示数据
                    $list = array_merge($list, $children);
                } else {
                    $list[] = $column;
                }
            }

        }
        return $list;
    }

    /**
     * 构建table dataList
     *
     * @return \think\Collection
     * @throws \Exception
     */
    public function buildData()
    {
        $this->handleQuery();
        $data = $this->rows;

        $columns = array_merge($this->getVisibleColumns(),[$this->getKeyColumn()]);
        $columns = $this->flattenColumns($columns);
        $data = collect($data)->map(function ($row) use ($columns) {
            return $this->resolveRow($row, $columns);
        });

        $paginate = $this->getPaginate();
        if ($paginate) {
            $data = $paginate->buildData($data);
        } else {
            $data = [
                'data' => $data
            ];
        }

        return $data;
    }


    /**
     * 默认显示字段
     *
     * @return array
     */
    public function visibleColumnKeys()
    {
        $columns = $this->flattenColumns($this->getVisibleColumns());
//        $columns = $this->getVisibleColumns();
        $keys = [];
        foreach ($columns as $column) {
            if (!in_array($column->name, $this->hideColumns)) {
                $keys[] = $column->name;
            }
        }

        return $keys;
    }


    /**
     * 处理row
     * @param $row
     * @param $columns
     * @return array
     */
    protected function resolveRow($row, $columns)
    {

        $item = [];
        $childrenKey = 'children';//子节点key
        $children = data_get($row, str_replace('->', '.', $childrenKey));

        if (is_array($children)) {
            foreach ($children as $child) {
                $item['_children'][] = $this->resolveRow($child, $columns);
            }
        }

        collect($columns)->each(function (Column $column) use ($row, &$item) {
            $columnNew = clone $column;
            $columnData = $columnNew->setRow($row);
            $item[$columnNew->name] = $columnData;
        });

        return $item;
    }

}
