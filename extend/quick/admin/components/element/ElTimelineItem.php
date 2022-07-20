<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\metable\HasSizeProps;
use quick\admin\Element;

class ElTimelineItem extends Element
{

    public $component = "el-time-line";


    /**
     * ElTimelineItem constructor.
     * @param string|Element $content
     * @param string $timestamp
     */
    public function __construct($content = '',string $timestamp = '')
    {
        $content && $this->content($content);
        $timestamp && $this->timestamp($timestamp);
    }


    /**
     * @param string|Element
     * @return $this
     */
    public function content( $content)
    {
        $this->props("content", $content);
        return $this;

    }


    /**
     * @param string $tiem
     * @return $this
     */
    public function timestamp(string $tiem)
    {
        $this->props("timestamp", $tiem);
        return $this;
    }


    /**
     * @param string $value
     * @return $this
     */
    public function icon(string $value)
    {
        $this->props(__FUNCTION__, $value);
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function color(string $value)
    {
        $this->props(__FUNCTION__, $value);
        return $this;
    }

    /**
     * 	时间戳位置 top / bottom
     * @param string $value
     * @return $this
     */
    public function placement(string $value)
    {
        $this->props(__FUNCTION__, $value);
        return $this;
    }


    /**
     * 节点类型
     *
     * primary / success / warning / danger / info
     * @param string $value
     * @return $this
     */
    public function type(string $value)
    {
        $this->props(__FUNCTION__, $value);
        return $this;
    }



    /**
     * 空心点
     * @return $this
     */
    public function hollow()
    {
        $this->props(__FUNCTION__, true);
        return $this;
    }


    /**
     * 居中
     * @return $this
     */
    public function center()
    {
        $this->props(__FUNCTION__, true);
        return $this;
    }

    /**
     * 隐藏时间戳
     * @return $this
     */
    public function hideTimestamp()
    {
        $this->props(__FUNCTION__, true);
        return $this;
    }



}
