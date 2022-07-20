<?php
declare (strict_types=1);

namespace quick\admin\form;

use quick\admin\form\fields\Cascader;
use quick\admin\form\fields\Checkbox;
use quick\admin\form\fields\Color;
use quick\admin\form\fields\CustomField;
use quick\admin\form\fields\Date;
use quick\admin\form\fields\DynamicField;
use quick\admin\form\fields\Field;
use quick\admin\form\fields\FooterField;
use quick\admin\form\fields\IconField;
use quick\admin\form\fields\Images;
use quick\admin\form\fields\InputNumber;
use quick\admin\form\fields\JsonField;
use quick\admin\form\fields\Quill;
use quick\admin\form\fields\Radio;
use quick\admin\form\fields\Rate;
use quick\admin\form\fields\Select;
use quick\admin\form\fields\SelectTag;
use quick\admin\form\fields\Slider;
use quick\admin\form\fields\SwitchField;
use quick\admin\form\fields\Text;
use quick\admin\form\fields\Time;
use quick\admin\form\fields\Transfer;
use quick\admin\form\fields\Tree2Field;
use quick\admin\form\fields\TreeField;
use quick\admin\form\fields\Upload;
use quick\admin\form\traits\HandleFieldsTraits;
use quick\admin\form\traits\HandlePanel;
use quick\admin\form\traits\HandleValidateTraits;
use quick\admin\Element;
use think\Exception;


/**
 * Class Form
 * @package form\form
 * @method Text text($column, $label = '')
 * @method Radio radio($column, $label = '')
 * @method CustomField custom($component, $column = '', $label = '')
 * @method Checkbox checkbox($column, $label = '')
 * @method InputNumber inputNumber($column, $label = '')
 * @method SwitchField switch ($column, $label = '')
 * @method Slider slider($column, $label = '')
 * @method Rate rate($column, $label = '')
 * @method Color color($column, $label = '')
 * @method Transfer transfer($column, $label = '')
 * @method Time time($column, $label = '')
 * @method Date date($column, $label = '')
 * @method Select select($column, $label = '')
 * @method Cascader cascader($column, $label = '')
 * @method TreeField tree($column, $label = '')
 * @method Tree2Field tree2($column, $label = '')
 * @method IconField icon($column, $label = '')
 * @method FooterField footer($column, $label = '')
 * @method DynamicField dynamic($column, $label = '')
 * @method JsonField json($column, $label = '')
 * @method Upload upload($column, $label = '')
 * @method Images images($column, $label = '')
 * @method Quill quill($column, $label = '')
 * @method SelectTag selectTag($column, $label = '')
 */
class Form extends Element
{

    use HandleFieldsTraits,
        HandlePanel,
        HandleValidateTraits;

    public $component = "quick-form";
    /**
     * 表单标题
     */
    public $title;

    /**
     * field
     * @var array
     */
    public $fields = [];


    /**
     * 可用的field
     *
     * @var array
     */
    public static $availableFields = [
        'custom' => CustomField::class,
        'text' => Text::class,
        'radio' => Radio::class,
        'checkbox' => Checkbox::class,
        'inputNumber' => InputNumber::class,
        'switch' => SwitchField::class,
        'slider' => Slider::class,
        'rate' => Rate::class,
        'color' => Color::class,
        'transfer' => Transfer::class,
        'time' => Time::class,
        'date' => Date::class,
        'select' => Select::class,
        'cascader' => Cascader::class,
        'tree' => TreeField::class,
        'tree2' => Tree2Field::class,
        'icon' => IconField::class,
        'footer' => FooterField::class,
        'dynamic' => DynamicField::class,
        'json' => JsonField::class,
        'upload' => Upload::class,
        'images' => Images::class,
        'quill' => Quill::class,
        'selectTag' => SelectTag::class,
    ];



    /**
     * Form constructor.
     * @param string $title
     * @param string $url
     */
    public function __construct(string $title = '', string $url = '')
    {
        $this->title = $title;
        $this->withAttributes(["label-width" => "auto"]);
        $url && $this->url($url);
    }


    /**
     * 单张图片
     * @param $column
     * @param string $label
     * @return Upload
     */
    public function image($column, $label = '')
    {
        return $this->images($column, $label)->max(1)->fillUsing(function ($inputs, $model, $column, $requestColumn){
            return isset($inputs[$requestColumn][0]) ? $inputs[$requestColumn][0]:'';
        });
    }


    /**
     * @param int $width
     * @return Form
     */
    public function labelWidth(int $width)
    {
        return $this->withAttributes(["label-width" => $width . "px"]);
    }


    /**
     * footer固定底部
     * @return Form
     */
    public function fixedFooter()
    {
        return $this->props('fixedFooter',true);
    }


    /**
     * footer固定底部
     * @return Form
     */
    public function hideReset()
    {
        return $this->props('showReset',false);
    }


    /**
     * footer固定底部
     * @return Form
     */
    public function hideCancel()
    {
        return $this->props('showCancel',false);
    }



    /**
     * 设置请求提交携带参数
     *
     * @param array $data
     * @return Form
     */
    public function extendData(array $data)
    {
        $info = $this->getProps('extendData',[]);
        return $this->props('extendData',array_merge($data,$info));
    }


    /**
     * @param string $position right/left/top
     * @return Form
     */
    public function labelPosition(string $position)
    {
        return $this->attribute('label-position', $position);
    }


    /**
     * form提交地址
     *
     * @param string $url
     * @return $this
     */
    public function url(string $url)
    {
        $this->props("submitUrl", $url);
        return $this;
    }


    /**
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $size medium / small / mini
     * @return $this
     */
    public function size(string $size)
    {
        $this->attribute("size", $size);
        return $this;
    }

    /**
     *  隐藏 footer
     *
     * @return $this
     */
    public function hideFooter()
    {
        $this->props('showFooter', false);
        return $this;
    }


    /**
     *  disabled
     *
     * @return $this
     */
    public function disabled()
    {
        $this->props('disabled', true);
        return $this;
    }

    /**
     * 行内表单
     * @return $this
     */
    public function inline()
    {
        $this->attribute("inline", true);
        return $this;
    }


    /**
     * @param $field
     * @return $this
     * @throws \Exception
     */
    public function appendField($field)
    {
        if (is_callable($field)) {
            call_user_func($field, $this);
        } else {
            $field = is_array($field) ? $field : [$field];
            foreach ($field as $item) {
                if (!($item instanceof Element)) {
                    throw new \Exception('field is not instanceof Element::class');
                }
            }
            $this->fields = array_merge($this->fields, $field);
        }

        return $this;
    }

    /**
     * get form fields
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * 过滤获取field组件类
     *
     * @return array
     */
    public function getFilterFields()
    {
        return $this->_filterFields($this->getFields());
    }

    /**
     * 过滤获取field组件类
     *
     * @param array $fields
     * @return array
     */
    protected function _filterFields(array $fields)
    {
        $data = [];
        /** @var Field $field */
        foreach ($fields as $field) {
            if(!($field instanceof Element)){
                continue;
            }
            $children = $field->getChildrenComponents();
            if (is_array($children) && count($children)) {
                $data = array_merge($data, $this->_filterFields($children));
            }
            if ($field instanceof Field) {
                $data[] = $field;
            }
        }
        return $data;
    }

    /**
     * 根据名称获取field class
     * @param $name
     * @return bool|mixed
     */
    public static function findFieldClass($name)
    {

        $class = static::$availableFields[$name] ?? '';
        if (!empty($class) && class_exists($class)) {
            return $class;
        }
        return false;
    }


    /**
     * @param array $config
     * @param Form|null $form
     * @return Form|null
     */
    public static function buildForm(array $config,?Form $form = null)
    {

//        $list = [
//            [
//                'type' => 'text',
//                'args' => [
//                    'name',
//                    '名称',
//                ],
//                'methods' => [
//                    [
//                        'type' => 'required',
//                        'args' => []
//                    ],
//                    [
//                        'type' => 'require22d',
//                        'args' => []
//                    ]
//                ]
//
//            ]
//        ];

        if(empty($form)){
            $form = self::make();
        }

        foreach ($config as $field) {
            if (is_callable([$form, $field['type']])) {
                $formItem = $form->{$field['type']}(...$field['args']);
                foreach ($field['methods'] as $item) {
                    if (is_callable([$formItem, $item['type']])) {
                        $formItem->{$item['type']}(...$item['args']);
                    }
                }
            }

        }
        return $form;

    }


    /**
     * @param $method
     * @param $arguments
     * @return bool
     * @throws \Exception
     */
    public function __call($method, $arguments)
    {
        if ($className = static::findFieldClass($method)) {
            $element = new $className(...$arguments);
            $element->setForm($this);
            $this->appendField($element);
            return $element;
        }
        throw new Exception("Field type [$method] does not exist.");
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->jsonSerialize();
    }


    /**
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->props([
            'fieldList' => $this->getFields()
        ]);
        return array_merge(parent::jsonSerialize(), [
            'title' => $this->title
        ]);
    }
}
