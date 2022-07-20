<?php

namespace quick\admin\table\traits;


use Closure;
use quick\admin\components\Component;
use quick\admin\Element;
use quick\admin\library\tools\StrTools;
use quick\admin\table\action\InlineInputColumn;
use quick\admin\table\action\SwitchColumn;
use quick\admin\table\action\TextColumn;
use quick\admin\table\column\AbstractColumn;
use quick\admin\table\column\DrawerColumn;
use quick\admin\table\column\IconColumn;
use quick\admin\table\column\ImageColumn;
use quick\admin\table\column\IndexColumn;
use quick\admin\table\column\ModalColumn;
use quick\admin\table\column\PopoverColumn;
use quick\admin\table\column\TagColumn;
use think\contract\Arrayable;
use think\helper\Str;

/**
 * Trait ExtendDisplay.
 *
 * @method IconColumn icon($size = '', $color = '')
 * @method TagColumn tag($types, $default = '', $effect = '')
 * @method TextColumn text($method, $text = '')
 * @method IndexColumn index($text = '')
 * @method SwitchColumn switch ($callback = '')
 * @method DrawerColumn drawer($callback = '')
 * @method ImageColumn image($width,$height)
 * @method InlineInputColumn inlineInput($text = '')
 *
 */
trait ExtendDisplayColumnTrait
{
    /**
     * 字段显示容器组件
     *
     * @var AbstractColumn
     */
    public $displayComponent;

    /**
     *
     * @var array
     */
    public static $displayComponents = [
        'icon' => IconColumn::class,
        'tag' => TagColumn::class,
        'text' => TextColumn::class,
        'index' => IndexColumn::class,
        'switch' => SwitchColumn::class,
        'drawer' => DrawerColumn::class,
        'image' => ImageColumn::class,
        'inlineInput' => InlineInputColumn::class,
    ];


    /**
     * @param int $limit
     * @param string $suffix
     * @return ExtendDisplayColumnTrait
     */
    public function limit(int $limit, string $suffix = '...')
    {
        return $this->display(function ($value) use ($limit, $suffix) {
            if (is_string($value)) {
                return limit($value, $limit, $suffix);
            }
            return $value;
        });
    }


    public function html()
    {
        return $this->display(function ($value) {
            return Component::html($value);
        });
    }


    /**
     * @return ExtendDisplayColumnTrait
     */
    public function ucfirst()
    {
        return $this->display(function ($value) {
            if (is_string($value)) {
                return Str::title($value);
            }
            return $value;
        });
    }


    /**
     * 扩展显示组件
     *
     * @param $name
     * @param $displayer
     */
    public static function extend($name, $displayer)
    {
        static::$displayComponents[$name] = $displayer;
    }


    /**
     * 设置显示组件
     *
     * @param AbstractColumn $displayComponent
     * @return $this
     */
    public function displayComponent(AbstractColumn $displayComponent)
    {
        $this->displayComponent = $displayComponent;

        return $this;
    }

    /**
     * 获取显示组件
     *
     * @return mixed
     */
    public function getDisplayComponent()
    {
        // todo 设置默认component
        return $this->displayComponent;
    }


    /**
     * @param string $default
     * @return $this
     */
    public function default($default = '-')
    {
        $this->display(function ($value) use ($default) {
            return $value ?: $default;
        });
        return $this;
    }


    /**
     * 定义应用于解析字段显示值的回调
     *
     * @param Closure $displayCallback
     * @return $this
     */
    public function display(Closure $displayCallback)
    {
        $this->displayCallbacks[] = $displayCallback;

        return $this;
    }


    /**
     *  使用组件显示column
     *
     * @param string $displayClass
     * @param array $arguments
     * @return mixed
     */
    public function displayUsing(string $displayClass, $arguments = [])
    {

        list($table, $column) = [$this->table, $this];

        /** @var AbstractColumn $object */
        $object = new $displayClass($table, $column, $this->name);
        $object->init(...$arguments);
        $object->setInitArgs($arguments);
        $this->displayComponent($object);// 显示组件不放在闭包里面是因为便于权限计算

        return $this;
    }


    /**
     * @param $abstract
     * @param $arguments
     * @return ExtendDisplayColumnTrait
     */
    protected function callSupportDisplay($abstract, $arguments)
    {
        return $this->display(function ($value) use ($abstract, $arguments) {
            if (is_array($value) || $value instanceof Arrayable) {
                return call_user_func_array([collect($value), $abstract], $arguments);
            }

            if (is_string($value)) {
                return call_user_func_array([StrTools::class, $abstract], array_merge([$value], $arguments));
            }
            return $value;
        });
    }

    /**
     * Call Builtin display.
     *
     * @param string $abstract
     * @param array $arguments
     *
     * @return $this
     */
    protected function callBuiltinDisplay($abstract, $arguments)
    {
        if ($abstract instanceof Closure) {
            return $this->display(function ($value, $row, $originalValue) use ($abstract, $arguments) {
                return $abstract->call($this, ...array_merge([$value, $row, $originalValue], $arguments));
            });
        }

        if (class_exists($abstract) && is_subclass_of($abstract, AbstractColumn::class)) {

            return $this->displayUsing($abstract, $arguments);
        }

        return $this;
    }


    /**
     * @param $method
     * @param $arguments
     * @return $this
     */
    public function __call($method, $arguments)
    {

        if (array_key_exists($method, static::$displayComponents)) {
            return $this->callBuiltinDisplay(static::$displayComponents[$method], $arguments);
        }
        return $this->callSupportDisplay($method, $arguments);
    }
}
