<?php
declare (strict_types=1);

namespace quick\admin\metable;


trait HasStyle
{


    /**
     * @var array
     */
    protected $style = [];


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function style($name, $value = '')
    {
        if (is_array($name)) {
            $this->withStyle($name);
        } else {
            $this->withStyle([$name => $value]);
        }
        return $this;
    }


    /**
     * @param array $style
     * @return $this
     */
    protected function withStyle(array $style)
    {
        $this->style = array_merge($this->style, $style);

        return $this;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    protected function getStyle($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->style;
        }
        return $this->style[$key] ?? $default;
    }


}
