<?php
declare (strict_types=1);

namespace quick\admin\components\layout;

use quick\admin\Element;

/**
 * Class Content
 */
class Col extends Element
{


    /**
     * @var string
     */
    public $component = "el-col";


    /**
     * Col constructor.
     * @param $content
     * @param int $width
     */
    public function __construct($content, $width = 24)
    {
        if ($content instanceof \Closure) {
            call_user_func($content, $this);
        } else {
            $this->children($content);
        }

        $this->width($width);
    }


    /**
     * @param int|array $width
     * @return Col
     */
    public function width($width)
    {
        if (is_numeric($width)) {
            $width = ["span" => (int)$width];
        }

        return $this->withAttributes($width);
    }


    /**
     * @param $content
     * @return Col
     */
    public function row($content)
    {
        if (!$content instanceof \Closure) {
            $row = Row::make($content);
        } else {
            $row = Row::make();
            call_user_func($content, $row);
        }
        return $this->children($row);
    }

}