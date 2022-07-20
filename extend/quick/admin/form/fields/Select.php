<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use Closure;
use quick\admin\Element;
use think\Exception;

class Select extends Field
{


    public $component = 'form-select-field';

    /**
     * @var
     */
    public $default;


    /**
     * @var
     */
    protected $options;

    /**
     * @var string
     */
    protected $keyName;

    /**
     * @var string
     */
    protected $labelName;

    /**
     * @var
     */
    protected $disabledName;

    /**
     * option显示组件
     *
     * @var Element
     */
    protected $optionDisplay;

    /**
     * 设置option显示组件
     *
     * @param Element $component
     * @return $this
     */
    public function optionDisplay(Element $component)
    {
        $this->optionDisplay = $component;
        return $this;
    }

    /**
     * @return bool|Element
     */
    protected function _getOptionDisplay()
    {
        return $this->optionDisplay ?? false;
    }


    /**
     * 可清空单选
     *
     * @return $this
     */
    public function clearable()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }

    /**
     * 启用多选
     *
     * @return $this
     */
    public function multiple()
    {
        $this->setValueType('array');
        $this->attribute(__FUNCTION__, true);
        return $this;
    }

    /**
     * 启用多选 合并为一段文字
     *
     * @return $this
     */
    public function collapseTags()
    {
        $this->multiple();
        $this->attribute('collapse-tags', true);
        return $this;
    }


    /**
     * @param $options
     * @param string|Closure $key
     * @param string $label
     * @param string|Closure $disabled
     * @return $this
     */
    public function options($options, $key = '', string $label = '', $disabled = 'disabled')
    {
        $this->options = $this->formatOptions($options, $key, $label, $disabled);
        return $this;
    }


    /**
     * @param string $name
     * @param array $groupOptions
     * @param string $key
     * @param string $label
     * @param string|Closure $disabled
     * @return $this
     * @throws Exception
     */
    public function group(string $name, array $groupOptions, $key = '', string $label = '', $disabled = 'disabled')
    {
        $data = ["label" => $name];


        $groupOptions = $this->formatOptions($groupOptions, $key, $label, $disabled);

        if (empty($groupOptions)) {
            throw new Exception("Group options cannot be empty");
        }
        $data['options'] = $groupOptions;
        $options = $this->options;
        if (is_array($options)) {
            if (!isset($options[0]['options'])) {
                $options = [[
                    'label' => '',
                    'options' => $options,
                ]];
            }
        } else {
            $options = [];
        }
        $this->options = array_merge($options, [$data]);
        return $this;
    }

    /**
     * 是否允许用户创建新条目，需配合 filterable 使用
     *
     * @return $this
     */
    public function filterable()
    {
        $this->attribute('filterable', true);
        return $this;
    }

    /**
     * @param array $options 选项
     * @param string $keyName 选项key
     * @param string $labelName 选项label
     * @param string|Closure $disabled 选项禁用字段
     * @return array
     */
    public function formatOptions(array $options, string $keyName = 'value', string $labelName = 'label', $disabled = "disabled")
    {
        if (empty($keyName) && empty($labelName)) {
            $list = [];

            foreach ($options as $k => $v) {
                if (isset($v['value']) && isset($v['label'])) {
                    $list = $options;
                    break;
                }
                $disabled = false;
                if (is_callable($disabled)) {
                    $disabled = call_user_func($disabled, $v);
                }
                if(is_numeric($k)){
                   $this->valueType = 'number';
                }
                $list[] = [
                    'value' => $k,
                    'label' => $v,
                    'disabled' => (bool)$disabled,
                ];
            }
            return $list;
        }

        $list = collect($options)->map(function ($item) use ($keyName, $labelName, $disabled) {

            if (is_callable($disabled)) {
                $disabled = call_user_func($disabled, $item);
            } else {
                $disabled = isset($item[$disabled]) ? $item[$disabled] : false;
            }
            if(is_numeric($item[$keyName])){
                $this->valueType = 'number';
            }
            return array_merge($item, [
                'value' => $item[$keyName],
                'label' => $item[$labelName],
                'disabled' => (bool)$disabled,
            ]);
        })->toArray();
        return $list;
    }

    /**
     * 远程搜索
     *
     * @return $this
     */
    public function remote(string $url)
    {
        $this->attribute('url', $url);
        $this->attribute('remote', true);
        $this->filterable();
        return $this;
    }

    /**
     * 多选且可搜索时，是否在选中一个选项后保留当前的搜索关键词
     *
     * @return $this
     */
    public function reserveKeyword()
    {
        $this->attribute("reserve-keyword", true);
        return $this;
    }

    /**
     * @return $this
     */
    public function allowCreate()
    {
        $this->filterable();
        $this->attribute('allow-create', true);
        $this->defaultFirstOption();
        return $this;
    }

    /**
     * 在输入框按下回车，选择第一个匹配项。需配合 filterable 或 remote 使用
     *
     * @return $this
     */
    public function defaultFirstOption()
    {
        $this->attribute('default-first-option', true);
        return $this;
    }

    /**
     * 远程加载时显示的文字
     *
     * @param string $value
     * @return $this
     */
    public function loadingText(string $value)
    {
        $this->attribute('loading-text', $value);
        return $this;
    }


    /**
     * @return array
     */
    public function getOptions()
    {
        $options = $this->options;
//        if(is_array($this->options)){
//            $options = $this->_formatOptions($this->options,$this->keyName,$this->labelName,$this->disabledName);
//        }
        return $options;
    }



    /**
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->attribute('options', $this->getOptions());
        $this->attribute('optionDisplay', $this->_getOptionDisplay());
        return array_merge(parent::jsonSerialize(), []);
    }
}
