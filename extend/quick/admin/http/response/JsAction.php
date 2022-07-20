<?php
declare (strict_types=1);

namespace quick\admin\http\response;


use JsonSerializable;



class JsAction implements JsonSerializable
{

    use HasAction;

    public $actions = [];



    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array_merge($this->actions);
    }

}
