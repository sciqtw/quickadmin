<?php


namespace quick\admin\components\metable;


trait HasWidthProps
{

    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->attribute("width",$width);
        return $this;
    }

}