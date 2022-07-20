<?php
declare (strict_types = 1);

namespace quick\admin\components\layout;

use quick\admin\components\Component;
use quick\admin\Element;



/**
 * Class Content
 */
class Content extends Element
{


    /**
     * @var string
     */
    public $title;

    /**
     * @var
     */
    public $description;

    /**
     * @var string
     */
    public $component = "quick-page";

    /**
     * 设置标题
     * @param string $title
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;
        return $this;
    }
    
    public function description(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param $body
     * @return $this
     */
    public function body($body)
    {
        return $this->row($body);
    }


    /**
     * @param $content
     * @return $this
     */
    public function row($content)
    {
        if ($content instanceof \Closure) {
            $row = Row::make();
            call_user_func($content, $row);
        } else {
            $row = Row::make($content);
        }
        $this->children($row);
        return $this;
    }

    /**
     * 隐藏header
     *
     * @return Content
     */
    public function hideHeader()
    {
        return $this->attribute(['showHeader' => false]);
    }

    public function jsonSerialize()
    {
        $this->props("title",$this->title);
        $this->props("description",$this->description);
        return array_merge( parent::jsonSerialize(),[]);
    }
}