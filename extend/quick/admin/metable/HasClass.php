<?php
declare (strict_types=1);

namespace quick\admin\metable;


trait HasClass
{


    /**
     * @var array
     */
    protected $class = [];


    /**
     * @param $name
     * @return $this
     */
    public function setClass($name)
    {
        if (is_array($name)) {
            $this->withClass($name);
        } else {
            $this->withClass([$name => true]);
        }
        return $this;
    }


    /**
     * @param array $class
     * @return $this
     */
    protected function withClass(array $class)
    {
        $this->class = array_merge($this->class, $class);

        return $this;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    protected function getClass($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->class;
        }
        return $this->class[$key] ?? $default;
    }


}
