<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

class ConfirmAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'confirm';


    /**
     * ConfirmAction constructor.
     * @param Actions $confirm
     * @param null $cancel
     * @param string $msg
     * @param string $title
     */
    public function __construct(Actions $confirm, $msg = '', $title = '', $cancel = null)
    {
        $this->confirm($confirm);
        $cancel && $this->cancel($cancel);
        $this->msg($msg);
        $this->title($title);
        $this->withParams(['attributes'=>['lockScroll' => false]]);
    }


    /**
     *
     * @param Actions $confirm
     * @return ConfirmAction
     */
    public function confirm(Actions $confirm)
    {
        return $this->withParams([__FUNCTION__ => $confirm]);
    }


    /**
     * @param Actions $cancel
     * @return ConfirmAction
     */
    public function cancel(Actions $cancel)
    {
        return $this->withParams([__FUNCTION__ => $cancel]);
    }

    /**
     * @param string $msg
     * @return ConfirmAction
     */
    public function msg(string $msg)
    {
        return $this->withParams([__FUNCTION__ => $msg]);
    }


    /**
     * @param string $title
     * @return ConfirmAction
     */
    public function title(string $title)
    {
        return $this->withParams([__FUNCTION__ => $title]);
    }

}
