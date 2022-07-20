<?php


namespace quick\admin\components\metable;


trait HasSizeProps
{

    /**
     * @param string $size medium / small / mini
     * @return $this
     */
    public function size(string $size)
    {
        $this->attribute("size",$size);
        return $this;
    }

}