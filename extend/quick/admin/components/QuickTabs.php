<?php
declare (strict_types=1);

namespace quick\admin\components;


use quick\admin\components\element\ElTabs;

class QuickTabs extends ElTabs
{

    public $component = "quick-tabs";


    /**
     *  去掉底部margin-bottom
     *
     * @return $this
     */
    public function removeBottom()
    {
        $this->props('remove-bottom',true);
        return $this;
    }


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
     * @return $this
     */
    public function isFilter()
    {
        $this->props('isFilter',true);
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
