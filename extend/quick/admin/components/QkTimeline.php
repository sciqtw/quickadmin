<?php
declare (strict_types=1);


namespace quick\admin\components;



use quick\admin\components\element\ElTimelineItem;
use quick\admin\Element;

class QkTimeline extends Element
{


    public $component = "qk-timeline";

    private $list = [];


    /**
     * @param $content
     * @param string $time
     * @return ElTimelineItem
     */
    public function add($content,string $time)
    {
        $item = ElTimelineItem::make($content,$time);
        $this->list[] = $item;
//        halt($this->list);
        return $item;
    }


    public function jsonSerialize()
    {
        $this->props('list',$this->list);
        return parent::jsonSerialize();
    }

}
