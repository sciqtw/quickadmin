<?php

namespace components\vcharts\src;

use quick\admin\QuickTool;

class VchartsTool extends QuickTool
{


    /**
     * @return array
     */
    public function script(): array
    {

        return [
            'vchart'=> __DIR__.'/../dist/js/field.js'
        ];
    }




    public function style(): array
    {
        return [
            'vchart'=> __DIR__.'/../dist/css/field.css'
        ];
    }
}
