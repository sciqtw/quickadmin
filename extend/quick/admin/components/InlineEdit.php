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
class InlineEdit extends Element
{


    public $component = 'inline-edit';


    /**
     * @param $content
     * @return InlineEdit|Element
     */
    public function content($content)
    {
        return $this->props([__FUNCTION__ => $content]);
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
