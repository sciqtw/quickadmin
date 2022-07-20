<?php
declare(strict_types=1);

namespace components\admin\src;


use quick\admin\Quick;
use quick\admin\QuickTool;

class AdminAuth extends QuickTool
{

    public $name = "auth";



    public function resource()
    {
        return [];
    }


    /**
     * @return array
     * @throws \ReflectionException
     */
    public function script(): array
    {
        Quick::resourcesIn($this->getAppKey(),__DIR__);
        return [];
    }

    public function style(): array
    {
        return [];
    }
}
