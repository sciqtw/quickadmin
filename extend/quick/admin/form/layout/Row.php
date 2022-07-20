<?php
declare (strict_types = 1);

namespace quick\admin\form\layout;

use quick\admin\Element;


/**
 * Class Content
 */
class Row extends Element
{


    /**
     * @var string
     */
    public $component = "el-row";


    /**
     * Row constructor.
     * @param string $content
     */
    public function __construct($content = '')
    {
        if (!empty($content)) {
            $this->col(24, $content);
        }
    }

    /**
     * @param $width
     * @param $content
     * @return $this
     */
    public function col($width, $content)
    {
        $col = Col::make($content, $width);
        return $this->addCol($col);
    }

    /**
     * @param Col $col
     * @return $this
     */
    protected function addCol(Col $col)
    {
        $this->children($col);
        return $this;
    }
}