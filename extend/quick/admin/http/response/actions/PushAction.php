<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class PushAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'push';


    /**
     * PushAction constructor.
     * @param string $path 地址
     * @param array $query 参数
     */
    public function __construct(string $path,array $query = [])
    {
        $this->path($path);
        !empty($query) && $this->query($query);
    }


    /**
     * 地址
     * @param string $path
     * @return $this
     */
    public function path(string $path)
    {
        $this->params = array_merge($this->params, ['path' => $path]);
        return $this;
    }


    /**
     * @param array|null $query
     * @return $this
     */
    public function query(array $query)
    {
        $this->params = array_merge($this->params, ['query' => $query]);
        return $this;
    }


}
