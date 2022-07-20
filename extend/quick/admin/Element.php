<?php
declare (strict_types = 1);

namespace quick\admin;


use JsonSerializable;
use quick\admin\metable\HasBuilderEvents;
use quick\admin\metable\HasClass;
use quick\admin\metable\HasDomProps;
use quick\admin\metable\HasEmit;
use quick\admin\metable\HasProps;
use quick\admin\metable\HasStyle;
use quick\admin\metable\Metable;

abstract class Element implements JsonSerializable
{

    use Metable,
        HasDomProps,
        HasClass,
        HasStyle,
        HasEmit,
        HasBuilderEvents,
        HasProps,
        AuthorizedToSee;


    /**
     *  组件名称.
     *
     * @var string
     */
    public $component;


    /**
     * 自定义组件
     *
     * @var array
     */
    protected static $customComponents = [];



    /**
     * Element constructor.
     * @param null $component
     */
    public function __construct($component = null)
    {
        $this->component = $component ?? $this->component;
        $this->callInitCallbacks();
    }


    /**
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }


    /**
     * 获取字段使用的组件名称
     *
     * @return string
     */
    public function component()
    {
        if (isset(static::$customComponents[get_class($this)])) {
            return static::$customComponents[get_class($this)];
        }

        return $this->component;
    }


    /**
     * 设置字段使用的前端组件
     *
     * @param $component
     */
    public static function useComponent($component)
    {
        static::$customComponents[get_called_class()] = $component;
    }


    /**
     * 获取当前组件的的子类
     *
     * @return array
     */
    public function getChildrenComponents()
    {
        if(is_array($this->children)){
            return $this->children;
        }
        return [$this->children];
    }


    /**
     * @param array $data
     * @return array
     */
    protected function getExtraAttrs($data = [])
    {

        if($slot = $this->getSlot()){
            $data['slot'] = $slot;
        }
        if($class = $this->getClass()){
            $data['class'] = $class;
        }
        if($style = $this->getStyle()){
            $data['style'] = $style;
        }
        if($domProps = $this->getDomProps()){
            $data['domProps'] = $domProps;
        }

        return $data;
    }


    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $props = array_merge($this->getProps(),$this->getAttributes(),$this->getExtraAttrs());
        return array_merge([
            'component' => $this->component(),
            'children' => $this->getChildren(),
//            'attributes' =>$this->getAttributes(),// component 组件的attrs
            'props' =>$props,// component 组件的props
//            'extraAttrs' =>$this->getExtraAttrs()   // component 组件的class style domProps slot key ref  refInFor
        ], $this->meta());
    }
}
