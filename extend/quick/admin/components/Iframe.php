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
class Iframe extends Element
{


    public $component = 'quick-iframe';


    public function __construct(string $url)
    {
        $this->url($url);
    }

    /**
     * @param string $url
     * @return Iframe
     */
    public function url(string $url)
    {
        return $this->attribute('src',$url);
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
