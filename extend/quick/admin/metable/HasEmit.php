<?php
declare (strict_types=1);

namespace quick\admin\metable;


trait HasEmit
{


    /**
     * @var array
     */
    protected $emit = [];


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function emit($name, $value = '')
    {
        if (is_array($name)) {
            $this->withEmit($name);
        } else {
            $this->withEmit([$name => $value]);
        }
        return $this;
    }


    /**
     * @param array $emit
     * @return $this
     */
    protected function withEmit(array $emit)
    {
        $this->emit = array_merge($this->emit, $emit);

        return $this;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    protected function getEmit($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->emit;
        }
        return $this->emit[$key] ?? $default;
    }


}
