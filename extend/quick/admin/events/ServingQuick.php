<?php
declare (strict_types = 1);

namespace quick\admin\events;


use think\Request;

class ServingQuick
{


    /**
     * @var Request
     */
    public $request;

    /**
     * ServingQuick constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
}
