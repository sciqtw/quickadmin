<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class MessageAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'message';



    /**
     * MessageAction constructor.
     * @param $msg
     * @param string $type
     */
    public function __construct($msg, $type = 'success')
    {
        $this->message($msg);
        $this->type($type);
    }


    /**
     * @param string $msg
     * @return $this
     */
    public function message(string $msg)
    {
        $this->params = array_merge($this->params, ['message' => $msg]);
        return $this;
    }


    /**
     * @param string $type success/warning/info/error
     * @return $this
     */
    public function type(string $type)
    {
        $this->params = array_merge($this->params, ['type' => $type]);
        return $this;
    }


}
