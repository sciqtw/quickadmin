<?php
declare (strict_types = 1);

namespace quick\admin\components;


use quick\admin\Element;

/**
 * 自定义组件
 *
 * Class Custom
 * @package quick\components
 */
class Custom extends Element
{


    public $component;

    /**
     * Custom constructor.
     * @param $component
     */
    public function __construct($component)
    {
        $this->component = $component;
    }
    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(),[]);
    }
}
