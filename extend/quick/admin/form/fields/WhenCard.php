<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\Element;
use think\Exception;

class WhenCard extends Element
{


    public $component = 'form-when-field';



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

    public function getDefault()
    {

        return $this->default;
    }


    /**
     * @param string $key
     * @return $this
     */
    public function emitKey(string $key)
    {
        $this->props('emitKey',$key);
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
