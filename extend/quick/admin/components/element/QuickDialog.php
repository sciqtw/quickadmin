<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;
use quick\admin\form\Form;

class QuickDialog extends Element
{


    public $component = "quick-dialog";

    /**
     * CustomField constructor.
     * @param $column
     * @param $title
     */
    public function __construct($title = '')
    {

        $title && $this->title($title);
        $this->props('type' ,'dialog');
        $this->attribute([
            "lock-scroll" => false,
            "top" => '10vh'
        ])->maxHeight("65vh")->width('750px');

    }


    /**
     * @param string $title
     * @return QuickDialog
     */
    public function title(string $title)
    {
        $this->withMeta(["title" => $title]);
        return $this->attribute(["title" => $title]);
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
     * @param string $width
     * @return $this
     */
    public function width(string $width)
    {
        is_numeric($width) && $width = $width . "px";
        $this->attribute("width", $width);
        return $this;
    }

    /**
     * @param $component
     * @param string $slot
     * @return Element
     */
    public function children($component, $slot = '')
    {
//        if ($component instanceof Form) {
//            $component->hideFooter();
//        }
        return parent::children($component, $slot);
    }

    /**
     * @param string $height
     * @return $this
     */
    public function height(string $height)
    {
        $this->attribute("height", $height);
        return $this;
    }

    /**
     * @param string $height
     * @return $this
     */
    public function maxHeight(string $height)
    {
        $this->attribute("max-height", $height);
        return $this;
    }

}
