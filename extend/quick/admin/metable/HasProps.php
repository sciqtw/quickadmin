<?php
declare (strict_types=1);

namespace quick\admin\metable;


trait HasProps
{


    /**
     * @var array
     */
    protected $props = [];


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function props($name, $value = '')
    {
        if (is_array($name)) {
            $this->withProps($name);
        } else {
            $this->withProps([$name => $value]);
        }
        return $this;
    }


    /**
     * @param array $props
     * @return $this
     */
    protected function withProps(array $props)
    {
        $this->props = array_merge($this->props, $props);

        return $this;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    protected function getProps($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->props;
        }
        return $this->props[$key] ?? $default;
    }


}
