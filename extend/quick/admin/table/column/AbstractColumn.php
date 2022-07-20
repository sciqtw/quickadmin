<?php
declare (strict_types=1);

namespace quick\admin\table\column;


use Closure;
use quick\admin\components\Component;
use quick\admin\components\QuickAction;
use quick\admin\components\QuickPopover;
use quick\admin\contracts\Renderable;
use quick\admin\http\response\actions\DialogAction;
use quick\admin\http\response\actions\InlineEditAction;
use quick\admin\http\response\actions\PopoverAction;
use quick\admin\http\response\JsonResponse;
use quick\admin\Quick;
use quick\admin\Element;
use quick\admin\table\Column;
use quick\admin\table\Table;
use quick\admin\table\traits\HasActionColumnTrait;
use think\helper\Str;

abstract class AbstractColumn extends Element
{
    use HasActionColumnTrait;


    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'index-show-field';

    /**
     * @var array
     */
    protected $displayCallbacks = [];

    /**
     * 字段名称
     *
     * @var string
     */
    protected $name;

    /**
     * column
     * @var
     */
    public $value;

    /**
     * 原值
     *
     * @var
     */
    public $originalValue;

    /**
     *  column 容器
     *
     * @var
     */
    protected $container;

    /**
     * @var Table
     */
    protected $table;

    /**
     * @var Column
     */
    protected $column;

    /**
     * 行数据
     * @var
     */
    protected $row;

    /**
     *  异步显示组件
     * @var
     */
    protected $asyncComponent;


    /**
     * 初始化传参
     *
     * @var array
     */
    protected $initArgs = [];


    /**
     * IndexColumn constructor.
     * @param $value
     * @param Table $table
     * @param Column $column
     * @param $row
     */
    public function __construct(Table $table, Column $column, $name)
    {
        $this->setTable($table);
        $this->setColumn($column);
        $this->setName($name);
    }

    /**
     * @param array $args
     */
    public function setInitArgs(array $args = [])
    {
        $this->initArgs = $args;
    }

    /**
     * @param string $value
     */
    public function init($value = '')
    {

    }


    /**
     *  获取操作的uri密钥。
     * @return string
     */
    public function uriKey()
    {
        return Str::snake(Quick::humanize($this)) . "_" . $this->name;;
    }


    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * @param $row
     * @return $this
     */
    public function setRow($row)
    {
        $this->row = $row;
        return $this;
    }


    /**
     * @param Table $table
     * @return $this
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
        return $this;
    }


    /**
     * @param Column $column
     * @return $this
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;
        return $this;
    }


    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     *  设置原始值
     *
     * @param $value
     * @return AbstractColumn
     */
    public function originalValue($value)
    {
        $this->originalValue = $value;
        return $this->withMeta(["originalValue" => $value]);
    }


    /**
     * 获取主键value
     * @return string
     */
    public function getPkValue()
    {
        $pkName = $this->table->getKey();
        return $this->row[$pkName] ?? '';
    }


    /**
     *  设置回调
     *
     * @param Closure $callback
     * @return $this
     */
    public function displayUsing(Closure $callback)
    {
        $this->displayCallbacks[] = $callback;
        return $this;
    }


    /**
     * @return $this
     */
    protected function callDisplayCallback()
    {
        foreach ($this->displayCallbacks as $displayCallback) {
            if ($displayCallback instanceof Closure) {
                $callback = Closure::bind($displayCallback, $this);
                call_user_func($callback, $this->value, $this->row, $this->originalValue);
            }
        }

        return $this;
    }


    /**
     * 尺寸
     *
     * @param string $value medium / small / mini
     * @return $this
     */
    public function size(string $value)
    {
        $this->withAttributes([__FUNCTION__ => $value]);
        return $this;
    }


    /**
     * @return $this
     */
    public function tooltip($conent)
    {
        $tooltip = Component::tooltip();
        if ($conent instanceof Closure) {
            call_user_func($conent, $tooltip);
        } else {
            $tooltip->content($conent);
        }
        return $this->container($tooltip);
    }


    /**
     * @param $conent
     * @return $this
     */
    public function inlineEdit($conent)
    {

        $inlineAction = InlineEditAction::make('');

        if ($conent instanceof Closure) {
            $this->displayUsing(function ($value, $row, $originalValue) use ($conent, $inlineAction) {
                $data = call_user_func($conent, $inlineAction->getInline(), $value, $row, $originalValue);
                if ($data) {
                    $inlineAction->getInline()->content($data);
                }
            });
        } else {
            $inlineAction->getInline()->content($conent);
        }

        $quickAction = QuickAction::make();
        $quickAction->action($inlineAction);

        return $this->container($quickAction);
    }


    /**
     * @param null $callback
     * @return $this
     */
    public function modal($callback = null)
    {

        $dialogAction = DialogAction::make('');
        $dialog = $dialogAction->getDialog()->props([
            "lock-scroll" => false,
        ])->maxHeight('65vh')->width('700px');

        if (func_num_args() == 2) {
            list($title, $callback) = func_get_args();
            if ($title instanceof Closure) {
                Closure::bind($title, $this);
                call_user_func($title, $dialog);
            } else {
                $dialog->title($title);
            }
        } elseif (func_num_args() == 1) {
            $dialog->title(__("title"));
        }


        if (is_subclass_of($callback, Renderable::class)) {
            $this->asyncComponent = $callback;
        }

        //设置图标
        $this->prefix(Component::icon('el-icon-Folder')
            ->color("#409EFF")->style('cursor', 'pointer'));


        $this->displayUsing(function ($value, $row, $originalValue) use ($callback, $dialog, $dialogAction) {
            if (!empty($this->asyncComponent)) {
                $dialogAction->content([
                    'url' => $this->loadUrl(),
                    'params' => [
                        static::$keyValue => $this->name,
                        static::$colValue => $value,
                    ],
                ]);
            }
        });

        if ($callback instanceof Closure) {
            $this->displayUsing(function ($value, $row, $originalValue) use ($callback, $dialog, $dialogAction) {
                $callback = Closure::bind($callback, $this);
                $data = call_user_func($callback, $dialog, $value, $row, $originalValue);
                if ($data && !$this->asyncComponent) {
                    $dialogAction->content($data);
                }
            });
        } elseif ($callback instanceof Element && !$this->asyncComponent) {
            $dialogAction->content($callback);
        }


        $quickAction = QuickAction::make();
        $quickAction->action($dialogAction);

        return $this->container($quickAction);
    }


    /**
     * @param string $conent
     * @return $this
     */
    public function popover($conent = '')
    {

        $Renderable = '';
        if (func_num_args() == 2) {
            list($conent, $Renderable) = func_get_args();
        }

        if (is_subclass_of($conent, Renderable::class)) {
            $Renderable = $conent;
        }

        if (is_subclass_of($Renderable, Renderable::class)) {
            $this->asyncComponent = $Renderable;
        }

        $popover = PopoverAction::make('');

        //设置图标
        $this->prefix(Component::icon('el-icon-Folder')
            ->color("#409EFF")->style('cursor', 'pointer'));


        $this->displayUsing(function ($value, $row, $originalValue) use ($conent, $popover) {
            if (!empty($this->asyncComponent)) {
                $popover->getPopover()->width(500)->request([
                    'url' => $this->loadUrl(),
                    'params' => [
                        static::$keyValue => $this->name,
                        static::$colValue => $value,
                    ],
                ]);
            }
        });

        if ($conent instanceof Closure) {
            $this->displayUsing(function ($value, $row, $originalValue) use ($conent, $popover) {
                $data = call_user_func($conent, $popover->getPopover(), $value, $row, $originalValue);
                if ($data) {
                    $popover->getPopover()->content($data);
                }
            });
        } else {
            $popover->getPopover()->content($conent);
        }

        $quickAction = QuickAction::make();
        $quickAction->action($popover);

        return $this->container($quickAction);
    }


    /**
     * 前缀内容
     *
     * @param Element $prefix
     * @return $this
     */
    public function prefix(Element $prefix)
    {
        $this->children($prefix, 'prefix');
        return $this;
    }

    /**
     * 后缀内容
     *
     * @param Element $prefix
     * @return $this
     */
    public function suffix(Element $suffix)
    {
        $this->children($suffix, 'suffix');
        return $this;
    }


    /**
     * 异步数据接口
     *
     * @return \quick\admin\http\response\ActionType|JsonResponse|\think\response\Json
     */
    public function async()
    {

        if ($asyncComponent = $this->asyncComponent) {
            /** @var Renderable $object */
            $object = new $asyncComponent();
            $res = $object->render();
            return JsonResponse::make()->success('success', $res)->send();
        }
        return JsonResponse::make()->error('error');

    }


    /**
     * @return mixed|string
     */
    public function loadUrl()
    {
        return $this->getUrl('async');
    }


    /**
     * 获取访问地址
     *
     * @param string $methods
     * @return string
     */
    public function getUrl(string $methods)
    {
        $module = str_replace('.', '/', app('http')->getName());
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();
        return "{$module}/resource/{$resource}/{$uriKey}/{$methods}";

    }


    /**
     * @param $content
     * @return $this
     */
    public function container($content)
    {
        $this->container = $content;
        return $this;
    }


    public function getContainer()
    {
        if (!$this->container) {
            $this->container = QuickAction::make();
        }
        return $this->container;
    }

    /**
     *  定义display
     *
     * @param string $value
     */
    public function display($value = '')
    {

    }


    /**
     *  设置资源管理器
     *
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }


    /**
     *  渲染
     *
     * @return array
     */
    public function render()
    {
        $this->callDisplayCallback();
        $initArgs = $this->initArgs ?? [];
        if ($value = $this->display(...$initArgs)) {
            $this->value = $value;
        }
        return $this->jsonSerialize();
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {

        $this->props(['value' => $this->value]);
        $data = array_merge(parent::jsonSerialize(),[
            'authorizedToEdit' => $this->isCanRun(),
            'uriKey' => $this->uriKey()
        ]);


        if ($this->container) {
            $container = $this->getContainer();
            if (method_exists($this->container, 'content')) {
                $container->content($data);
            }
            return $container->jsonSerialize();
        }


        return $data;


    }
}
