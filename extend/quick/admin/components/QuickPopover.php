<?php
declare (strict_types=1);

namespace quick\admin\components;


use quick\admin\components\element\ElPopover;

/**
 * Class QuickPopover
 * @package quick\admin\components
 */
class QuickPopover extends ElPopover
{


    public $component = "quick-popover";


    /**
     * 异步请求
     * @param string|array $value
     * @return QuickPopover
     */
    public function request($value)
    {
        return $this->props(__FUNCTION__,$value);
    }


}
