<?php
declare (strict_types = 1);

namespace quick\admin\form\fields;




class CustomField extends Field
{


    public $component;

    /**
     * CustomField constructor.
     * @param $column
     * @param $title
     */
    public function __construct($component,$column = '',$title = '')
    {
        $this->component = $component;
        $this->column = $column;
        $this->title = $title ?: $column;
        $this->init();

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
