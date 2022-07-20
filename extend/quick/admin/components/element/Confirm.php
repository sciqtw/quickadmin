<?php
declare (strict_types = 1);

namespace quick\admin\components\element;


use quick\admin\Element;
use quick\admin\http\response\ActionType;

class Confirm extends Element
{


    public $component = "confirm";

    /**
     * Confirm constructor.
     * @param $msg
     * @param ActionType $confirm
     * @param ActionType|null $cancel
     * @param null $title
     */
    public function __construct($msg, ActionType $confirm, $cancel = null, $title = null)
    {

        $title && $this->withMeta(["title" => $title]);
        $this->withAttributes(
            [
                "lockScroll" => false,
                "confirmButtonText" => "确定",
                "cancelButtonText" => "取消",
            ]
        );
        $this->msg($msg);
        $this->confirm($confirm);
        if($cancel instanceof ActionType){
            $this->cancel($cancel);
        }

    }

    /**
     * @param string $msg
     * @return $this
     */
    public function msg(string $msg)
    {
        $this->withMeta(["msg" => $msg]);
        return $this;
    }

    /**
     *  确认动作
     */
    public function confirm(ActionType $confirm)
    {
        $this->withMeta(["confirm" => $confirm]);
        return $this;
    }

    /**
     * 取消动作
     * @param $cancel
     * @return $this
     */
    public function cancel(ActionType $cancel)
    {
        $this->withMeta(["cancel" => $cancel]);
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
