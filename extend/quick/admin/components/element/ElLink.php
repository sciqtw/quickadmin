<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\metable\HasSizeProps;
use quick\admin\Element;

class ElLink extends Element
{

    public $component = "el-link";


    /**
     * ElLink constructor.
     * @param string $type
     */
    public function __construct(string $type = '')
    {
        $type && $this->type($type);
    }


    /**
     * @param string $type primary / success / warning / danger / info
     * @return $this
     */
    public function type(string $type)
    {
        $this->attribute("type", $type);
        return $this;

    }


    /**
     * @param string $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->attribute("icon", $icon);
        return $this;
    }


    /**
     * @param string $url
     * @return $this
     */
    public function href(string $url)
    {
        $this->attribute(__FUNCTION__, $url);
        return $this;
    }


    /**
     * @return $this
     */
    public function disabled()
    {
        $this->attribute(__FUNCTION__, true);
        return $this;
    }


    /**
     * @return $this
     */
    public function underline()
    {
        $this->attribute(__FUNCTION__, false);
        return $this;
    }

}
