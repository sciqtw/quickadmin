<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

use quick\admin\Element;

class PopConfirmAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'popconfirm';


    private $props = [];


    /**
     * ConfirmAction constructor.
     * @param Actions $confirm
     * @param null $cancel
     * @param string|array|Element $msg
     *  异步内容接收一个requestAction 同步内容接收Element
     * @param string $title
     */
    public function __construct(Actions $confirm, $content = '', $title = '', $cancel = null)
    {
        $this->confirm($confirm);
        $cancel && $this->cancel($cancel);
        $this->content($content);
        $this->title($title);
    }


    /**
     * @param $name
     * @param string $value
     * @return $this
     */
    public function props($name, $value = '')
    {

        if (is_array($name)) {
            $data = array_merge($this->props, $name);
        } else {
            $data = [$name => $value];
        }
        $this->props = array_merge($this->props, $data);

        return $this;
    }


    /**
     * @param Actions $confirm
     * @return PopConfirmAction
     */
    public function confirm(Actions $confirm)
    {
        return $this->withParams([__FUNCTION__ => $confirm]);
    }


    /**
     * @param Actions $cancel
     * @return PopConfirmAction
     */
    public function cancel(Actions $cancel)
    {
        return $this->withParams([__FUNCTION__ => $cancel]);
    }


    /**
     *  内容
     * @param string|array|Element $msg
     *  异步内容接收一个requestAction 同步内容接收Element
     * @return PopConfirmAction
     */
    public function content($msg)
    {
        return $this->withParams([__FUNCTION__ => $msg]);
    }


    /**
     * @param string $title
     * @return PopConfirmAction
     */
    public function title(string $title)
    {
        return $this->withParams([__FUNCTION__ => $title]);
    }


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        $this->withParams(['props' => $this->props]);
        return parent::jsonSerialize();
    }

}
