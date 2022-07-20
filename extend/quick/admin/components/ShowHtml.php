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
class ShowHtml extends Element
{


    public $component = 'show-html';


    public function __construct(string $html)
    {
        $this->html($html);
    }

    public function html(string $html)
    {
        return $this->attribute('html',$html);
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
