<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\Element;
use quick\admin\http\response\ActionType;

class ElPopover extends Element
{


    public $component = "el-popover";

    /**
     * ElPopover constructor.
     * @param string $content
     */
    public function __construct($content = '')
    {

        $this->content($content);
    }


    /**
     *  触发方式 click/focus/hover/manual
     *
     * @param string $value click/focus/hover/manual
     * @return $this
     */
    public function trigger(string $value)
    {
        $this->attribute(__FUNCTION__, $value);
        return $this;
    }

    /**
     * @return $this
     */
    public function click()
    {
        return $this->trigger(__FUNCTION__);
    }

    /**
     * @return $this
     */
    public function focus()
    {
        return $this->trigger(__FUNCTION__);
    }

    /**
     * @return $this
     */
    public function hover()
    {
        return $this->trigger(__FUNCTION__);
    }

    /**
     * @return $this
     */
    public function manual()
    {
        return $this->trigger(__FUNCTION__);
    }

    /**
     * 标题
     *
     * @param string $value
     * @return $this
     */
    public function title(string $value)
    {
        $this->attribute(__FUNCTION__, $value);
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function content($value)
    {
        $this->props([__FUNCTION__ => $value]);
        return $this;
    }

    /**
     * 宽度
     *
     * @param $value
     * @return $this
     */
    public function width($value)
    {
        $this->attribute(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 出现位置
     *
     *
     * @param string $value top/top-start/top-end/bottom/bottom-start/bottom-end/left/left-start/left-end/right/right-start/right-end
     * @return $this
     */
    public function placement(string $value)
    {
        $this->attribute(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 出现位置top
     *
     * @return $this
     */
    public function top()
    {
        $this->placement("top");
        return $this;
    }


    /**
     * 出现位置top-start
     *
     * @return $this
     */
    public function topStart()
    {
        $this->placement("top-start");
        return $this;
    }

    /**
     * 出现位置top-start
     *
     * @return $this
     */
    public function topEnd()
    {
        $this->placement("top-end");
        return $this;
    }

    /**
     * 出现位置bottom
     *
     * @return $this
     */
    public function bottom()
    {
        $this->placement("bottom");
        return $this;
    }


    /**
     * 出现位置bottom-start
     *
     * @return $this
     */
    public function bottomStart()
    {
        $this->placement("bottom-start");
        return $this;
    }

    /**
     * 出现位置bottom-end
     *
     * @return $this
     */
    public function bottomEnd()
    {
        $this->placement("bottom-end");
        return $this;
    }


    /**
     * 出现位置left
     *
     * @return $this
     */
    public function left()
    {
        $this->placement("left");
        return $this;
    }


    /**
     * 出现位置left-start
     *
     * @return $this
     */
    public function leftStart()
    {
        $this->placement("left-start");
        return $this;
    }

    /**
     * 出现位置bottom-end
     *
     * @return $this
     */
    public function leftEnd()
    {
        $this->placement("left-end");
        return $this;
    }

    /**
     * 出现位置right
     *
     * @return $this
     */
    public function right()
    {
        $this->placement("right");
        return $this;
    }

    /**
     * 出现位置right-start
     *
     * @return $this
     */
    public function rightStart()
    {
        $this->placement("right-start");
        return $this;
    }

    /**
     * 出现位置right-end
     *
     * @return $this
     */
    public function rightEnd()
    {
        $this->placement("right-end");
        return $this;
    }


    /**
     * 触发方式为 hover 时的显示延迟，单位为毫秒
     *
     * @param int $num
     * @return $this
     */
    public function openDelay(int $num)
    {
        $this->attribute("open-delay", $num);
        return $this;
    }

    /**
     * 触发方式为 hover 时的隐藏延迟，单位为毫秒
     *
     * @param int $num
     * @return $this
     */
    public function closeDelay(int $num)
    {
        $this->attribute("close-delay", $num);
        return $this;
    }



    /**
     * 禁用
     *
     * @return $this
     */
    public function disabled()
    {
        $this->attribute([__FUNCTION__ => true]);
        return $this;
    }


    /**
     * 出现位置的偏移量
     *
     * @param int $num
     * @return $this
     */
    public function offset(int $num)
    {
        $this->attribute([__FUNCTION__ => $num]);
        return $this;
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), []);
    }
}
