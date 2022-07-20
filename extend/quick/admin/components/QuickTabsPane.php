<?php
declare (strict_types=1);

namespace quick\admin\components;


use quick\admin\Element;

class QuickTabsPane extends Element
{

    public $component = "quick-tabs-pane";



    /**
     * @param string $value
     * @return $this
     */
    public function default(string $value)
    {
        $this->props('default',$value);
        return $this;
    }


    /**
     * @param string $value
     * @return $this
     */
    public function tabKey(string $value)
    {
        $this->props('tabKey',$value);
        return $this;
    }

}
