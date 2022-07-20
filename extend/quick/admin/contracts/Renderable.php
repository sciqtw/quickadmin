<?php
declare (strict_types=1);

namespace quick\admin\contracts;


use quick\admin\Element;

abstract class Renderable extends Element
{
    /**
     * @return Element
     */
    abstract function render():Element;
}
