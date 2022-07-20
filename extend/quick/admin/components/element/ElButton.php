<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\metable\HasSizeProps;
use quick\admin\Element;

class ElButton extends Element
{

    use HasSizeProps;

    public $component = "el-button";


    /**
     * ElButton constructor.
     * @param string $content
     * @param string $type
     * @param string $size
     */
    public function __construct(string $content, string $type = '', string $size = '')
    {
        $this->content($content);
        $type && $this->type($type);
        $size && $this->size($size);
    }


    /**
     * @return $this
     */
    public function disabled()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }


    /**
     * @param string $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->attribute("icon", $icon);
        return $this;
    }


    /**
     * @param string $type primary / success / warning / danger / info / text
     * @return $this
     */
    public function type(string $type)
    {
        if($type == 'text'){
            $this->attribute("text", true);
            $this->attribute("type", 'primary' );
            $this->style("padding", '2px' );
            $this->size('small');
            return $this;
        }
        $this->attribute("type", $type);
        return $this;

    }

    /**
     * 圆角按钮
     *
     * @return $this
     */
    public function round()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }

    /**
     * 圆形按钮
     *
     * @return $this
     */
    public function circle()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }

    /**
     * 朴素按钮
     *
     * @return $this
     */
    public function plain()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }
}
