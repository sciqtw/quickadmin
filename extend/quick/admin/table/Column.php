<?php
declare (strict_types=1);

namespace quick\admin\table;


use quick\admin\components\Component;
use quick\admin\Quick;
use quick\admin\Element;
use quick\admin\table\traits\ExtendDisplayColumnTrait;
use quick\admin\table\traits\HandleColumnTrait;
use quick\admin\table\traits\ExtendDisplayColumnHasTrait;
use think\helper\Str;
use think\Model;

class Column extends Element
{
    use HandleColumnTrait,
        ExtendDisplayColumnHasTrait,
        ExtendDisplayColumnTrait;


    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'quick-column';


    /**
     *  标题
     *
     * @var
     */
    protected $title;

    /**
     *  column name
     *
     * @var
     */
    public $name;

    /**
     * 当前值
     *
     * @var
     */
    protected $value;

    /**
     *  默认隐藏
     *
     * @var bool
     */
    protected $hide = false;

    public $pkValue = 0;

    /**
     * 原值
     *
     * @var
     */
    protected $originalValue;

    /**
     * 关联
     *
     * @var
     */
    public $relation;

    /**
     *  关联字段
     *
     * @var
     */
    public $relationColumn;

    /**
     *  行数据
     *
     * @var array|Model
     */
    protected $row;

    /**
     *  toble对象
     *
     * @var Table
     */
    protected $table;


    /**
     * Column constructor.
     * @param $name
     * @param string $title
     */
    public function __construct($name, $title = '')
    {
        if ($name instanceof \Closure) {
//            $this->name = $title;
            call_user_func($name, $this);
        } else {
            $this->name = $name;
            $title = empty($title) ? Str::title($name):$title;
        }
        $this->title = $title;
        $this->callInitCallbacks();
    }


    /**
     *  获取操作的uri密钥。
     * @return string
     */
    public function uriKey()
    {
        return Str::snake(Quick::humanize($this)) . "_" . $this->name;
    }


    /**
     * 设置name
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;
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
     * @param string|number $pkValue 主键value
     * @return $this
     */
    public function setPkValue($pkValue)
    {
        $this->pkValue = $pkValue;
        return $this;
    }


    /**
     * @return int
     */
    public function getPkValue()
    {
        return $this->pkValue;
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
     * 扩展column
     *
     * @param string $content
     * @return $this
     */
    public function expand($content = '')
    {
        if (is_callable($content)) {
            $this->display(function ($value, $row, $originalValue) use ($content) {
                return call_user_func($content,$value, $row);
            });
        } elseif (!empty($content)) {
            $this->display(function ($value, $row, $originalValue) use ($content) {
                return $content;
            });
        }
        $this->attribute("type", "expand");
        return $this;
    }


    /**
     * 设置title
     *
     * @param string $title 标题名称
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }


    /**
     * 设置头部
     *
     * @param Element $header
     * @return $this
     */
    public function header(Element $header)
    {
        $this->children($header, "header");
        return $this;
    }


    /**
     * 设置column width
     *
     * @param int $value
     * @return Column
     */
    public function width(int $value)
    {
        return $this->withAttributes(["width" => $value . "px"]);
    }


    /**
     *  排序
     *
     * @param bool $custom 是否后端查询
     * @return Column
     */
    public function sortable(bool $custom = false)
    {
        $custom = $custom ? 'custom' : true;
        return $this->withAttributes(["sortable" => $custom]);
    }


    /**
     *  添加一个子column
     *
     * @param $name
     * @param string $title
     * @param string $closure
     * @return $this
     */
    public function column($name, $title = '')
    {
        $column = new static($name, $title);
        $this->children($column);
        return $column;
    }


    /**
     * 列是否固定在左侧或者右侧，true 表示固定在左侧
     *
     * @param bool|string $position true, left, right
     * @return $this
     */
    public function fixed($position = true)
    {
        $this->attribute("fixed", $position);
        return $this;
    }


    /**
     *  固定在左边
     *
     * @return $this
     */
    public function fixLeft()
    {
        return $this->fixed('left');
    }


    /**
     *  固定在右边
     *
     * @return $this
     */
    public function fixRight()
    {
        return $this->fixed('right');
    }


    /**
     * 设置隐藏
     * @return $this
     */
    public function hide()
    {
        $this->table->hideColumns([$this->name]);
        return $this;
    }


    /**
     *  是隐藏
     *
     * @return bool
     */
    public function isHide()
    {
        return $this->hide;
    }


    /**
     * slot字段
     * @return Column
     */
    public function isSlot()
    {
        return $this->slot($this->name);
    }



    /**
     * 设置关联信息
     *
     * @param string $relation
     * @param string $relationColumn
     *
     * @return $this
     */
    public function setRelation($relation, $relationColumn = null)
    {
        $this->relation = $relation;
        $this->relationColumn = $relationColumn;
        return $this;
    }


    /**
     * If this column is relation column.
     *
     * @return bool
     */
    protected function isRelation()
    {
        return (bool)$this->relation;
    }


    /**
     * 获取当前类的子类
     *
     * @return array
     */
    public function getChildrenComponents(): array
    {
        return array_merge(
            (array)$this->getChildren(),
            [$this->getDisplayComponent()]
        );
    }

    /**
     *  返回渲染表头配置
     * @return array
     */
    private function renderColumnHeader()
    {
        $this->props([
            'title' => $this->title,
            'name' => $this->name,
            'uriKey' => $this->uriKey(),
        ]);
        return array_merge(parent::jsonSerialize(),
            [
                'title' => $this->title,
                'name' => $this->name,
                'uriKey' => $this->uriKey(),
            ]
        );
    }


    /**
     *  返回显示配置
     * @return array
     */
    private function renderColumnDisplay()
    {
        $this->resolveColumnValue();
        return [
            'title' => $this->title,
            'name' => $this->name,
            'uriKey' => $this->uriKey(),
            'value' => $this->value,
            'originalValue' => $this->originalValue,
            'display' => $this->resolveDisplayComponent()
        ];
    }


    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {

        if (empty($this->row)) {
            return $this->renderColumnHeader();
        }
        return $this->renderColumnDisplay();

    }
}
