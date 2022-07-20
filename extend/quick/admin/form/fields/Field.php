<?php
declare (strict_types=1);

namespace quick\admin\form\fields;

use quick\admin\form\Form;
use quick\admin\form\traits\FillField;
use quick\admin\form\traits\HandleFieldEvent;
use quick\admin\form\traits\Resolve;
use quick\admin\form\traits\ValidateField;
use quick\admin\Element;

class Field extends Element
{

    use Resolve,
        FillField,
        HandleFieldEvent,
        ValidateField;

    /**
     * 组件tag
     * @var
     */
    public $component;

    /**
     * 组件类型
     * @var string
     */
    protected $componentType = 'formItem';

    /**
     * 表单标题
     */
    protected $title;

    /**
     * 字段
     * @var
     */
    protected $column;

    /**
     * 默认值
     * @var
     */
    protected $default;

    /**
     * @var Form
     */
    protected $form;

    /**
     *  数据类型
     * @var string
     */
    protected $valueType = 'string';

    /**
     * Field constructor.
     * @param string $column
     * @param string $title
     */
    public function __construct(string $column, string $title = '')
    {
        $this->column = $column;
        $this->title = $title ?: $column;
        $this->init();
    }

    public function init()
    {
        return $this;
    }

    /**
     * @return Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param Form $form
     * @return $this
     */
    public function setForm(Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * 设置title
     *
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * 获取title
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title ?? $this->column;
    }

    /**
     * 获取column
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }


    /**
     * 获取前端验证规则
     *
     * @return array
     */
    public function getValidate()
    {
        return $this->transformRulesToVue($this->getRules());
    }


    /**
     * 设置默认值
     * @param $value
     * @return $this
     */
    public function default($value)
    {
        $this->default = $value;
        return $this;
    }


    /**
     * 获取默认值
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 设置提示信息
     *
     * @param string $value
     * @return $this
     */
    public function help(string $value)
    {
        $this->props('helpText', $value);
        return $this;
    }

    /**
     * 设置尺寸
     *
     * @param string $value medium|small|mini
     * @return $this
     */
    public function size(string $value)
    {
        $this->attribute('size', $value);
        return $this;
    }


    /**
     * @param $width
     * @return $this
     */
    public function width($width){
        $width = is_numeric($width) ? $width.'px':$width;
        $this->props('width',$width);
        return $this;
    }


    /**
     * 禁用
     *
     * @return $this
     */
    public function disabled()
    {
        $this->props('disabled', true);
        return $this;
    }

    /**
     * 设置只读状态
     *
     * @return $this
     */
//    public function readonly()
//    {
//        $this->props('readonly', true);
//        return $this;
//    }

    /**
     * @return $this
     */
    public function hidden()
    {
        $this->attribute("showField", false);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function placeholder(string $value)
    {
        $this->attribute([__FUNCTION__ => $value]);
        return $this;
    }


    /**
     * @return string
     */
    protected function getDefaultValue()
    {
        $value = $this->value !== null ? $this->value : $this->getDefault();
        if ($this->valueType == 'number' && is_numeric($value)) {
            $value = $value + 0;
        }
        return $value;
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 设置值类型
     *
     * @param string $type
     * @return $this
     */
    public function setValueType(string $type)
    {
        $this->valueType = $type;
        return $this;
    }


    /**
     * @param int $width
     * @return Field
     */
    public function labelWidth(int $width)
    {
        return $this->props('label-width',"{$width}px");
    }


    /**
     * @param int $width
     * @return Field
     */
    public function hiddenLabel()
    {
        return $this->props('hiddenLabel',true);
    }


    /**
     * 获取值类型
     *
     * @return string
     */
    public function getValueType()
    {
        return $this->valueType;
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->props("rules", $this->getValidate());
        $this->props("title", $this->title);
        $this->props("column", $this->column);
        $this->props("default", $this->getDefaultValue());
        $this->props(['emit' => $this->getEmit()]);
        return array_merge(parent::jsonSerialize(), []);
    }
}
