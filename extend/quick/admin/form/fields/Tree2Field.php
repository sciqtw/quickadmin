<?php
declare (strict_types=1);

namespace quick\admin\form\fields;



class Tree2Field extends Field
{


    public $component = 'form-tree2-field';

    public $default = [];


    /**
     * @param $value
     * @return mixed
     */
    public function transform($value)
    {
        return json_decode($value,true);
    }




    /**
     * @param array $options
     * @return TreeField
     */
    public function options(array $options)
    {

        return $this->props('data', $this->_formatOptions($options));
    }


    /**
     * 默认全部展开
     *
     * @param bool $expand
     * @return TreeField
     */
    public function expandAll($expand = true){
    //        default-expand-all
        return $this->props('expand-all',$expand);
    }


    /**
     *  缩进
     *
     * @param int $indent
     * @return TreeField
     */
    public function indent(int $indent)
    {
        return $this->attribute(__FUNCTION__,$indent);
    }


    /**
     *  叶节点最小宽度
     *
     * @param int $width
     * @return TreeField
     */
    public function minWidth(int $width)
    {
        return $this->props("min-width",$width."px");
    }


    /**
     * 格式化参数
     *
     * @param array $lists
     * @return array
     */
    protected function _formatOptions(array $lists)
    {
        foreach ($lists as $key => $arr) {
            $lists[$key] = $this->_hasChild($arr);
        }
        return $lists;
    }


    /**
     * 判断节点子节点是否是最后节点
     * @param $arr
     * @return bool
     */
    protected function _hasChild($arr)
    {
        $arr['inline'] = true;
        if(isset($arr['children']) && count($arr['children'])){
            foreach($arr['children'] as $key => $item){
                if (isset($item['children']) && count($item['children'])) {
                    $arr['inline'] = false;
                    $arr['children'][$key] =  $this->_hasChild($item);
                }
            }
        }
        return $arr;
    }


    /**
     * @return array
     */
    public function getDefault():array
    {
        return $this->default;
    }


    /**
     * @return string
     */
    protected function getDefaultValue()
    {
        return is_array($this->value) ? $this->value : $this->getDefault();
    }

    /**
     * 默认值
     * @param number $value
     * @return $this|Field
     */
    public function default($value)
    {
        $this->default = $value;
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
