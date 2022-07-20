<?php
declare (strict_types=1);

namespace quick\admin\table\column;



use quick\admin\components\Component;
use quick\admin\components\element\QuickIcon;

class IconColumn extends AbstractColumn
{

    /**
     * @var QuickIcon
     */
    public $icon;

    /**
     * 扩展类初始化方法
     * @param string $size
     * @return $this|mixed
     */
    public function init($size = '')
    {
        $this->icon = Component::icon('');
        if (func_num_args() == 2) {
            list($size, $color) = func_get_args();
            $color && $this->icon->color($color);
        }
        $size && $this->icon->size((string)$size);
        return $this;
    }

    /**
     * @param string $size
     * @param string $color
     * @return \quick\admin\components\element\QuickIcon|void
     */
    public function display($size = '',$color = '')
    {
        $value = $this->value ? $this->value:'';
        return $this->icon->icon($value);
    }

}
