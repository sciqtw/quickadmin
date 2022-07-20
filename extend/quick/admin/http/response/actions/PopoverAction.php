<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

use Closure;
use quick\admin\components\Component;
use quick\admin\Element;


/**
 * Class PopoverAction
 * @package quick\admin\http\response\actions
 */
class PopoverAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'popover';


    /**
     * @var \quick\admin\components\QuickPopover
     */
    private $popover;


    /**
     * ModalAction constructor.
     * @param Element $component
     * @param string|Closure|array $title
     */
    public function __construct($content, $title = '')
    {
        $popover = $this->getPopover();

        if ($title instanceof \Closure) {
            call_user_func($title, $popover);
        }elseif(is_array($title)){
            $popover->props($title);
        } elseif(is_string($title)){
            $popover->title($title);
        }
        $popover->content($content);
        $this->popover = $popover;
    }


    /**
     * @return \quick\admin\components\QuickPopover
     */
    public function getPopover()
    {
        if (!$this->popover) {
            $popover = Component::quickPopover('title');
            $this->popover = $popover;
        }

        return $this->popover;
    }


    /**
     * @param Element|string|array $component
     * 请求链接 请求对象
     * @return PopoverAction
     */
    public function content($component)
    {
        $popover = $this->getPopover();
        $popover->content($component);
        return $this;
    }

    /**
     * @param string $title
     * @return DialogAction
     */
    public function title(string $title)
    {
        return $this->withParams([__FUNCTION__ => $title]);
    }

    public function jsonSerialize()
    {
        $this->withParams(['config' => $this->getPopover()]);
        return parent::jsonSerialize();
    }

}
