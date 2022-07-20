<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;

class ElTag extends Element
{


    public $component = "el-tag";


    /**
     * ElTag constructor.
     * @param string $type
     * @param null $content
     */
    public function __construct(string $type = '',$content = null)
    {
        $content && $this->children($content);
        $type && $this->type($type);
    }


    /**
     * @param string $type
     * @return ElTag
     */
    public function type(string $type)
    {
        return $this->props('type',$type);
    }



}
