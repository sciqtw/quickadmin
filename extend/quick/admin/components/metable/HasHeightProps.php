<?php


namespace quick\admin\components\metable;


trait HasHeightProps
{

    /**
     * @param string $height
     * @return $this
     */
    public function height(string $height)
    {
        $this->attribute("height",$height);
        return $this;
    }

}