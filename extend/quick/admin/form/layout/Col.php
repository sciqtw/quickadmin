<?php
declare (strict_types = 1);

namespace quick\admin\form\layout;

use quick\admin\Element;
use quick\admin\form\Form;

/**
 * Class Content
 */
class Col extends Element
{

    
    /**
     * @var string
     */
    public $component = "el-col";

    /**
     * Col constructor.
     * @param $content
     * @param int $width
     */
    public function __construct($content, $width = 24)
    {
        if(is_array($width)){
            $this->attribute($width);
        }else{
            $width = $width < 1 ? round(24 * $width) : $width;
            $this->withAttributes(["span"=>$width]);
        }

        if ($content instanceof \Closure) {
            $content = \Closure::bind($content,$this);
            $form = Form::make("form");
            call_user_func($content,$form);
            $this->children($form->getFields());
        } else {
            $this->children($content);
        }

    }


}