<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;

class IconField extends Field
{


    public $component = 'form-icon-field';

    public $default = [];




    /**
     * @return array
     */
    public function getDefault():array
    {
        return $this->default;
    }



    /**
     * 默认值
     * @param number $value
     * @return $this|Field
     */
    public function default($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
