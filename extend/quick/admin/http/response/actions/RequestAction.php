<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class RequestAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'request';


    /**
     * RequestAction constructor.
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $data
     */
    public function __construct(string $url, $method = 'get', $params = [], $data = [])
    {
        return $this->request($url, $method, $params, $data);
    }


    /**
     * @param string $url
     * @param array $params
     * @return $this
     */
    public function get(string $url, array $params = [])
    {
        return $this->request($url, 'GET', $params);
    }


    /**
     * @param string $url
     * @param array $data
     * @return $this
     */
    public function post(string $url, array $data)
    {
        return $this->request($url, 'POST', [], $data);
    }


    /**
     * @param string $url
     * @param string $method
     * @param array $params
     * @param array $data
     * @return $this
     */
    public function request(string $url, $method = 'GET', array $params = [], array $data = [])
    {
        $this->params = array_merge($this->params, ['url' => $url]);
        $this->method($method);
        !empty($params) && $this->params($params);
        !empty($data) && $this->data($data);
        return $this;
    }


    /**
     * @param string $method
     * @return $this
     */
    public function method(string $method)
    {
        $this->params = array_merge($this->params, ['method' => $method]);
        return $this;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->params = array_merge($this->params, ['data' => $data]);
        return $this;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function params(array $params)
    {
        $this->params = array_merge($this->params, ['params' => $params]);
        return $this;
    }

}
