<?php
declare (strict_types=1);

namespace quick\admin\metable;

use quick\admin\Element;

trait Metable
{
    /**
     * 元素的基础属性.
     *
     * @var array
     */
    protected $meta = [];

    /**
     *  属性
     * @var array
     */
    protected $attributes = [];


    /**
     * @var array
     */
    protected $extraAttrs = [];

    /**
     * 子组件|内容
     * @var array|string
     */
    protected $children = [];


    /**
     * 获取元素的属性.
     *
     * @return array
     */
    public function meta()
    {
        return $this->meta;
    }


    /**
     * @param string $key
     * @param string $default
     * @return array|mixed|string
     */
    public function getAttributes($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->attributes;
        }
        return $this->attributes[$key] ?? $default;
    }


    /**
     *  设置属性
     * @param array|string $name 属性
     * @param string $value 属性值
     * @return $this
     */
    public function attribute($name, $value = '')
    {
        if (is_array($name)) {
            $this->withAttributes($name);
        } else {
            $this->withAttributes([$name => $value]);
        }
        return $this;
    }


    /**
     * 为元素设置其他元信息
     *
     * @param array $meta
     * @return $this
     */
    public function withMeta(array $meta)
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }


    /**
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function getMeta($key = '', $default = '')
    {
        if (empty($key)) {
            return $this->meta;
        }
        return $this->meta[$key] ?? $default;
    }


    /**
     * 添加属性
     *
     * @param array $attributes
     * @return $this
     */
    public function withAttributes(array $attributes)
    {
        $this->attributes = array_merge($this->attributes, $attributes);
        return $this;
    }


    /**
     * 设置slot
     * @param $slot
     * @return $this
     */
    public function slot(string $slot)
    {
        $this->withMeta(["slot" => $slot]);
        return $this;
    }


    /**
     * @return mixed|string
     */
    protected function getSlot()
    {
        return $this->getMeta('slot');
    }



    /**
     * @param $component
     * @param string $slot 插槽位置
     * @return $this
     */
    public function children($component, string $slot = '')
    {
        if (is_array($component)) {
            $this->children = is_array($this->children) ? array_merge($this->children, $component) : [$component];
        } else if ($component instanceof Element) {
            $children = $this->children;
            if (!empty($slot)) {
                //先去除旧的插槽组件
                $children = collect($children)->filter(function ($item) use ($slot) {
                    return !($item instanceof Element && $item->getSlot() == $slot);
                })->toArray();
                $component->slot($slot);
            }

            $this->children = array_merge((array)$children, [$component]);
        } else {
            $this->children = $component;
        }

        return $this;
    }


    /**
     * 组件内容
     *
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->children = $content;
        return $this;
    }


    /**
     * @param $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }


    /**
     * get Children
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }


    /**
     * set array mate
     * @param $array
     * @param $key
     * @param $name
     * @param $value
     * @return mixed
     */
    private function _withArray($array, $key, $name, $value)
    {
        !is_array($name) && $name = [$name => $value];
        if (isset($array[$key]) && is_array($array[$key])) {
            $array[$key] = array_merge($array[$key], $name);
        } else {
            $array[$key] = $name;
        }
        return $array;
    }
}
