<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;

class QuickIcon extends Element
{


    public $component = "quick-icon";


    public function __construct(string $icon = '',string $size = '',string $color= '')
    {
        $icon && $this->icon($icon);
        $size && $this->size($size);
        $color && $this->color($color);
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
     * @param string $size
     * @return $this
     */
    public function size(string $size)
    {
        $this->attribute("size", $size);
        return $this;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function color(string $color)
    {
        $this->attribute("color", $color);
        return $this;
    }
}
