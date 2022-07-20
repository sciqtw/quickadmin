<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;
use quick\admin\form\Form;

class ElDrawer extends Element
{


    public $component = "el-drawer";


    /**
     * ElDrawer constructor.
     * @param string $title
     */
    public function __construct($title = '')
    {

        $title && $this->withMeta(["title" => $title]);
        $this->props('type' ,'drawer');
        $this->props([
            "append-to-body" => false
        ])->size('50%');

    }


    /**
     * @param string $title
     * @return QuickDialog
     */
    public function title(string $title)
    {
        $this->withMeta(["title" => $title]);
        return $this->props(["title" => $title]);
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }


    /**
     * @param string $size
     * @return $this
     */
    public function size(string $size)
    {
        $this->props("size", $size);
        return $this;
    }

    /**
     * Drawer 打开的方向
     *
     * @param string $direction rtl / ltr / ttb / btt
     * @return $this
     */
    public function direction(string $direction)
    {
        $this->props("direction", $direction);
        return $this;
    }

    /**
     * @param $component
     * @param string $slot
     * @return Element
     */
    public function children($component, $slot = '')
    {

        return parent::children($component, $slot);
    }



}
