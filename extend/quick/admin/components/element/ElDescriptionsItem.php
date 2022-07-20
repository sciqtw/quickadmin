<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;

class ElDescriptionsItem extends Element
{


    public $component = "el-descriptions-item";


    /**
     * ElDescriptionsItem constructor.
     * @param string $label
     * @param string $content
     */
    public function __construct($label = '',$content = '')
    {
        $this->label($label);
        $this->content($content);
    }


    /**
     * @param $label
     * @return ElDescriptionsItem
     */
    public function label($label)
    {
        if($label instanceof Element){
            return $this->children($label,'label');
        }
        return $this->props('label',$label);
    }


    /**
     * @param string $value
     * @return ElDescriptionsItem
     */
    public function span(string $value)
    {
        return $this->props(__FUNCTION__,$value);
    }


    /**
     * @param int $value
     * @return ElDescriptionsItem
     */
    public function width(int $value)
    {
        return $this->props(__FUNCTION__,$value);
    }


    /**
     * @param int $value
     * @return ElDescriptionsItem
     */
    public function minWidth(int $value)
    {
        return $this->props('min-width',$value);
    }


    /**
     * @param string $value
     * @return ElDescriptionsItem
     */
    public function align(string $value)
    {
        return $this->props('align',$value);
    }


    /**
     * @return ElDescriptionsItem
     */
    public function left()
    {
        return $this->align('align','left');
    }

    /**
     * @return ElDescriptionsItem
     */
    public function center()
    {
        return $this->align('align','center');
    }

    /**
     * @return ElDescriptionsItem
     */
    public function right()
    {
        return $this->align('align','right');
    }


    /**
     * 列的内容自定义类名
     * @param string $value
     * @return ElDescriptionsItem
     */
    public function className(string $value)
    {
        return $this->props('className',$value);
    }


    /**
     *
     * @param string $value
     * @return ElDescriptionsItem
     */
    public function labelClassName(string $value)
    {
        return $this->props('labelClassName',$value);
    }
}
