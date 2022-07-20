<?php
declare (strict_types=1);

namespace quick\admin\table;


use think\Collection;

class Query
{
    /**
     * @var \think\Model
     */
    protected $model;

    /**
     * 原model
     *
     * @var \think\Model
     */
    protected $originalModel;

    /**
     * 查询条件集合
     *
     * @var \think\Collection
     */
    protected $queries;

    /**
     * 是否使用分页
     *
     * @var bool
     */
    protected $usePaginate = true;


    /**
     * @var string
     */
    protected $perPageName = 'per_page';

    /**
     * 20 items per page as default.
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * @var \Closure
     */
    protected $collectionCallback;


    protected $with = [];


    /**
     * Query constructor.
     * @param  $model
     */
    public function __construct($model)
    {
        $this->setModel($model);
        $this->originalModel = $model;
        $this->queries = collect();

    }


    /**
     * 关联预加载
     *
     * @param $name
     * @param string $val
     * @return $this
     */
    public function setWith($name,$val = '')
    {
        $with = empty($val) ? [$name]:[$name => $val];

        $this->with = array_merge($this->with,$with);
        return $this;
    }


    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;
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
     * 获取查询语句集合
     *
     * @return Collection
     */
    public function getQueries(): Collection
    {
        return $this->queries;
    }

    /**
     * 禁止分页
     *
     * @return $this
     */
    public function disablePagination()
    {
        $this->usePaginate = false;
        return $this;
    }


    /**
     *
     */
    protected function setPaginate()
    {
        $paginate = $this->findQueryByMethod('paginate');

        $this->queries = $this->getQueries()->filter(function ($query) {
            return $query['method'] != 'paginate';
        });

        if (!$this->usePaginate) {
            $query = [
                'method' => 'select',
                'arguments' => [],
            ];
        } else {

            $query = [
                'method' => 'paginate',
                'arguments' => $this->resolvePerPage($paginate),
            ];
        }

        $this->getQueries()->push($query);
    }

    /**
     * 处理分页
     *
     * @param $paginate
     * @return array|mixed
     */
    protected function resolvePerPage($paginate)
    {
        if ($perPage = request()->param($this->perPageName)) {
            if (is_array($paginate)) {
                $paginate['arguments'][0] = (int)$perPage;

                return $paginate['arguments'];
            }
            $this->perPage = (int)$perPage;
        }
        if (isset($paginate['arguments'][0])) {
            return $paginate['arguments'];
        }

        return [$this->perPage];
    }


    /**
     * @param $method
     * @return mixed
     */
    protected function findQueryByMethod($method)
    {
        return $this->queries->first(function ($query) use ($method) {
            return $query['method'] == $method;
        });
    }


    /**
     * @return \think\Model
     */
    public function getOriginalModel()
    {
        return $this->originalModel;
    }

    /**
     * 获取数据模型
     *
     * @return \think\Model
     */
    public function eloquent()
    {
        return $this->model;
    }

    /**
     * @return \think\Model
     */
    protected function getQueryModel()
    {

//        if ($this->relation) {
//            $this->model = $this->relation->getQuery();
//        }

        if(!empty($this->with)){
            $this->model = $this->model->with($this->with);
        }

        $this->setPaginate();

        $this->getQueries()->each(function ($query) {
            $this->model = call_user_func_array([$this->model, $query['method']], $query['arguments']);
            return $query;
        });

        return $this->model;

    }

    /**
     * @param \Closure|null $callback
     * @return $this
     */
    public function collection(\Closure $callback = null)
    {
        $this->collectionCallback = $callback;

        return $this;
    }

    /**
     * 构建查询数据
     * @return Collection
     * @throws \Exception
     *
     */
    public function buildData($toArray = false)
    {

        if (empty($this->data)) {
            $collection = $this->getQueryModel();
            if ($this->collectionCallback) {
                $collection = call_user_func($this->collectionCallback, $collection);
            }

            if ($toArray) {
                $this->data = $collection->toArray();
            } else {
                $this->data = $collection;
            }
        }



        return $this->data;

    }

    /**
     * 添加查询条件
     * @param array $conditions
     * @return $this
     */
    public function addConditions(array $conditions)
    {
        foreach ($conditions as $condition) {
            call_user_func_array([$this, key($condition)], current($condition));
        }

        return $this;
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->getQueries()->push([
            'method' => $method,
            'arguments' => $arguments,
        ]);

        return $this;
    }


    /**
     *  todo
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
//        $data = $this->buildData();
        $data = [];

        if (array_key_exists($key, $data)) {
            return $data[$key];
        }
    }
}
