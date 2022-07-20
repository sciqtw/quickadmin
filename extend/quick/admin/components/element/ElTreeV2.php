<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\Component;
use quick\admin\Element;

class ElTreeV2 extends Element
{

    public $component = "el-tree-v2";


    /**
     * ElTreev2 constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->props('data',$data);

    }


    /**
     * @param string $type card/border-card
     * @return $this
     */
    public function type(string $type)
    {
        $this->attribute("type", $type);
        return $this;

    }


}
