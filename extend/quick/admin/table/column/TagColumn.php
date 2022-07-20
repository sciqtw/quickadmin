<?php
declare (strict_types=1);

namespace quick\admin\table\column;


class TagColumn extends AbstractColumn
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'index-tag-field';

    /**
     * @var array
     */
    protected $types = [];

    /**
     * 默认类型
     *
     * @var string
     */
    protected $defaultType = '';


    /**
     * @param string $type
     * @return $this|mixed
     */
    public function init($type = '')
    {
        if (func_num_args() == 3) {
            list($type, $default, $effect) = func_get_args();
        } elseif (func_num_args() == 2) {
            list($type, $default) = func_get_args();
            $effect = '';
        } elseif (func_num_args() == 1) {
            list($type) = func_get_args();
            $default = $effect = '';
        }

        if ($type instanceof \Closure) {
            $this->displayUsing($type);
        } else {
            $this->type($type, $default);
        }
        $effect && $this->effect($effect);
        return $this;
    }

    /**
     *  主题
     *
     * @param string $value dark / light / plain
     * @return $this
     */
    public function effect(string $value)
    {
        $this->withAttributes([__FUNCTION__ => $value]);
        return $this;
    }

    /**
     *  light 主题
     *
     * @return $this
     */
    public function light()
    {
        return $this->effect(__FUNCTION__);
    }

    /**
     * dark 主题
     *
     * @return $this
     */
    public function dark()
    {
        return $this->effect(__FUNCTION__);
    }

    /**
     * plain 主题
     *
     * @return $this
     */
    public function plain()
    {
        return $this->effect(__FUNCTION__);
    }

    /**
     * @param string|array $type success|info|warning|danger
     * @param string $default success|info|warning|danger
     * @return $this
     */
    public function type($type, $default = '')
    {
        if (is_array($type)) {
            $this->types = $type;
            !empty($default) && $this->defaultType = $default;
        } else {
            $this->defaultType = $type;
        }
        return $this;
    }


    /**
     * 背景色
     *
     * @param string $color
     * @return $this
     */
    public function color(string $color)
    {
        $this->withAttributes([__FUNCTION__ => $color]);
        return $this;
    }

    public function display($value = null)
    {
        list($tags, $value, $originalValue) = [[], (array)$this->value, $this->originalValue];
//        $list = empty($originalValue) ? $value:$originalValue;
        foreach ($value as $key => $item) {
//            $type = isset($this->types[$item]) ? $this->types[$item]:(isset($this->types[$key]) ?  $this->types[$key]:$this->defaultType);
            $type = $this->types[$item] ?? ($this->types[$this->originalValue] ?? $this->defaultType);
            $tags[] = [
                'type' => $type,
                'value' => $item,
            ];
        }
        $this->props([
            "tags" => $tags,
            "tagProps" => $this->attributes,
        ]);

        return parent::display();
    }

}
