<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;

class ElCard extends Element
{


    public $component = "el-card";


    /**
     * ElCard constructor.
     * @param Element|null $content
     */
    public function __construct($content = null)
    {
        $content && $this->children($content);
    }


    /**
     * 设置头部内容
     * @param Element $header 头部内容
     * @return $this
     */
    public function header(Element $header)
    {
        $this->children($header, "header");
        return $this;
    }


}
