<?php

namespace quick\admin\table\traits;


use quick\admin\components\Component;
use quick\admin\exceptions\AdminException;
use quick\admin\form\fields\Field;
use quick\admin\form\Form;
use quick\admin\table\column\AbstractColumn;
use think\helper\Arr;

/**
 * Trait HasDisplayColumnTrait
 * @package quick\admin\table\column
 */
trait ExtendDisplayColumnHasTrait
{

    /**
     * @param string $format
     * @return $this
     */
    public function date(string $format = 'Y-m-d')
    {
        $this->display(function ($value) use ($format){
            if(strtotime($value)){
                return date($format,strtotime($value));
            }
            return date($format,$value);
        });
        return $this;
    }


    /**
     * @param array $statusList [success|processing|default|error|warning]
     * @param string $default success|processing|default|error|warning
     * @return \quick\admin\components\Custom
     */
    public function dot(array $statusList = [],string $default = 'success')
    {

        $this->display(function ($value, $row, $originalValue) use ($statusList,$default){
            $originalValue = $originalValue ? $originalValue:$value;
            $status =   (!empty($originalValue) && isset($statusList[$originalValue]) ? $statusList[$originalValue]:$default);
            return Component::custom('quick-badge')->props([
                'status' => $status,
                'text' => $value,
            ]);
        });

    }


    /**
     * @param $types
     * @param string $default
     * @return $this
     */
    public function label($types, $default = '', $effect = '')
    {
        $this->tag($types, $default, $effect);
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function tooltip($content)
    {
        $this->index(function () use ($content) {
            $this->tooltip($content);
        });
        return $this;
    }


    /**
     * @param $content
     * @return $this
     */
    public function popover($content)
    {
        $argc = func_get_args();
        /** @var AbstractColumn $displayComponent */
        $displayComponent = $this->getDisplayComponent();
        if (!$displayComponent) {
            $this->index();
            $displayComponent = $this->getDisplayComponent();

        }
        $displayComponent->popover(...$argc);

        return $this;
    }


    /**
     * @param $content
     * @return $this
     */
    public function modal($content)
    {
        $argc = func_get_args();
        /** @var AbstractColumn $displayComponent */
        $displayComponent = $this->getDisplayComponent();
        if (!$displayComponent) {
            $this->index();
            $displayComponent = $this->getDisplayComponent();
        }
        $displayComponent->modal(...$argc);
        return $this;
    }


    /**
     * @param $content
     * @return $this
     */
    public function inline($content)
    {
        $argc = func_get_args();
        /** @var AbstractColumn $displayComponent */
        $displayComponent = $this->getDisplayComponent();
        if (!$displayComponent) {
            $this->index();
            $displayComponent = $this->getDisplayComponent();
        }
        $displayComponent->inlineEdit(...$argc);
        return $this;
    }

    public function input($rule = '')
    {
        return $this->inlineInput("text", $rule);
    }

    public function number($rule = '')
    {
        return $this->inlineInput("number", $rule);
    }

    public function text1($rule = '')
    {
        return $this->inlineInput("text1", $rule);
    }


    public function select(array $options)
    {
        $argc = func_get_args();
        return $this->inlineInput('select', $options);
    }


    /**
     * 内容映射
     *
     * @param array $values
     * @param null $default
     * @return $this
     */
    public function using(array $values, $default = null)
    {
        $this->display(function ($value) use ($values, $default) {
            if (is_null($value)) {
                return $default;
            }
            return Arr::get($values, $value, $default);
        });
        return $this;
    }


    /**
     * 内容替换
     *
     * @param array $arr
     * @return mixed
     */
    public function replace(array $arr)
    {
        return $this->display(function ($value) use ($arr) {
            if (isset($arr[$value])) {
                return $arr[$value];
            }
            return $value;
        });
    }


    /**
     * @param string $template
     * @param array $data
     * @return mixed
     */
    public function view(string $template,array $data = [])
    {
        return $this->display(function ($value, $row) use ($template, $data) {
            return Component::html(view($template, array_merge(['value' => $value, 'model' => $row], $data))->getContent());
        });
    }


    /**
     * @param \Closure $closure
     * @return mixed
     */
    public function field(\Closure $closure)
    {
        $form = Form::make();
        $field = call_user_func($closure, $form);
        if ($field instanceof Field) {
            return $this->display(function ($value) use ($field) {
                return $field->default($value);
            });
        }
        throw new AdminException('');
    }
}
