<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\Component;
use quick\admin\Element;

class ElDescriptions extends Element
{


    public $component = "el-descriptions";


    /**
     * ElDescriptions constructor.
     * @param string $title
     * @param null|string|Element $extra
     */
    public function __construct($title = '',$extra = null)
    {
        $title && $this->title($title);
        $extra && $this->extra($extra);
    }


    /**
     * @param string $type
     * @return ElTag
     */
    public function type(string $type)
    {
        return $this->props('type',$type);
    }


    /**
     * 带有边框
     * @return ElDescriptions
     */
    public function border()
    {
        return $this->props('border',true);
    }


    /**
     * 一行 Descriptions Item 的数量
     *
     * @param int $num
     * @return ElDescriptions
     */
    public function column(int $num)
    {
        return $this->props('column',$num);
    }


    /**
     * direction	排列的方向	string	vertical / horizontal	horizontal
     *
     * @param string $value
     * @return ElDescriptions
     */
    public function direction(string $value)
    {
        return $this->props('direction',$value);
    }


    /**
     * direction	排列的方向	string	vertical / horizontal	默认horizontal
     * @return ElDescriptions
     */
    public function vertical()
    {
        return $this->direction('vertical');
    }


    /**
     * 标题文本，显示在左上方
     *
     * @param string $value
     * @return ElDescriptions
     */
    public function title(string $value)
    {
        if($value instanceof Element){
            return $this->children($value,'title');
        }
        return $this->props('title',$value);
    }


    /**
     * 操作区文本，显示在右上方
     *
     * @param string|Element $value
     * @return ElDescriptions
     */
    public function extra($value)
    {
        if($value instanceof Element){
            return $this->children($value,'extra');
        }
        return $this->props('extra',$value);
    }


    /**
     * @param $item
     * @param $label
     * @param $content
     * @return ElDescriptionsItem
     */
    protected function addItem($item,$label,$content)
    {
        if($label instanceof Element){
            $label = clone $label;
            $label->props('data',$item);
        }else{
            $label = $item[$label] ?? '';
        }


        if($content instanceof Element){
            $content = clone $content;
            $content->props('data',$content);
        }else{
            $content = $item[$content] ?? '';
        }

        return ElDescriptionsItem::make($label,$content);
    }

    /**
     * @param array $data
     * @param $label
     * @param $content
     * @return $this
     */
    public function data(array $data,$label = 'label',$content = 'content')
    {
        $list = [];
        foreach($data as $item){
            $list[]  = $this->addItem($item,$label,$content);
        }

        !empty($list) && $this->children($list);

        return $this;
    }
}
