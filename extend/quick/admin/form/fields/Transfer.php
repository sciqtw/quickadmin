<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class Transfer extends Field
{


    public $component = 'form-transfer-field';

    /**
     * @var
     */
    public $default;



    /**
     * 设置数据
     *
     * @param array $data
     * @param string|\Closure $disabled
     * @param string $keyName
     * @param string $labelName
     * @return $this
     */
    public function data(array $data,$disabled ="disabled",$keyName = 'key',$labelName = "label")
    {
        $this->attribute("data",$this->_formatData($data,$keyName,$labelName,$disabled));
        return $this;
    }

    /**
     * 设置数据
     *
     * @param array $data 数据源
     * @param string $keyName key字段名称
     * @param string $labelName label字段名称
     * @param \Closure|string $disabled 禁用字段 或 闭包计算
     * @return \think\Collection
     */
    protected function _formatData(array $data,string $keyName,string $labelName,$disabled)
    {
        $list = collect($data)->map(function ($item) use ($keyName,$labelName,$disabled){

            if(is_callable($disabled)){
                $disabled =  call_user_func($disabled,$item);
            }else{
                $disabled =  isset($item[$disabled]) ? $item[$disabled]:false;
            }
            return [
                'key' => $item[$keyName],
                'label' => $item[$labelName],
                'disabled' => $disabled,
            ];
        });
        return $list;
    }

    /**
     * 可搜索
     *
     * @return $this
     */
    public function filterable()
    {
        $this->attribute("filterable", true);
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function filterPlaceholder(string $text)
    {
        $this->attribute("filter-placeholder", $text);
        return $this;
    }

    /**
     * 自定义按钮文案
     *
     * @param array $texts
     * @return $this
     */
    public function buttonTexts(array $texts)
    {
        $this->attribute("button-texts", $texts);
        return $this;
    }

    /**
     * 初始状态下左侧列表的已勾选项的 key 数组
     * @param array $checked
     * @return $this
     */
    public function leftDefault(array $checked)
    {
        $this->attribute("left-default-checked", $checked);
        return $this;
    }

    /**
     * 初始状态下右侧列表的已勾选项的 key 数组
     * @param array $checked
     * @return $this
     */
    public function rightDefault(array $checked)
    {
        $this->attribute("right-default-checked", $checked);
        return $this;
    }

    /**
     * @param array $format
     * @return $this
     */
    public function format(array $format)
    {
        $this->attribute("format", $format);
        return $this;
    }

    /**
     * @param array $titles
     * @return $this
     */
    public function titles(array $titles)
    {
        $this->attribute("titles", $titles);
        return $this;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
