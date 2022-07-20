<?php
declare (strict_types=1);

namespace quick\admin\metable;


trait HasDomProps
{


    /**
     * @var array
     */
    protected $domProps = [];


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function domProps($name, $value = '')
    {
        if (is_array($name)) {
            $this->withDomProps($name);
        } else {
            $this->withDomProps([$name => $value]);
        }
        return $this;
    }


    /**
     * @param array $props
     * @return $this
     */
    protected function withDomProps(array $props)
    {
        $this->domProps = array_merge($this->props, $props);

        return $this;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    protected function getDomProps($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->domProps;
        }
        return $this->domProps[$key] ?? $default;
    }


}
