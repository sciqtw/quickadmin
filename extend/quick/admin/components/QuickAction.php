<?php
declare (strict_types=1);

namespace quick\admin\components;


use quick\admin\Element;
use quick\admin\http\response\actions\Actions;
use quick\admin\http\response\actions\PopConfirmAction;
use quick\admin\http\response\JsAction;

/**
 *
 * @method  $this message($msg, $type = 'success')
 * @method  $this push($path, $query = '')
 * @method  $this download($link, $name = '')
 * @method  $this redirect($url)
 * @method  $this openInNewTab($url)
 * @method  $this request($url, $method = 'get', $params = [], $data = [])
 * @method  $this get($url, $params = [])
 * @method  $this post($url, $params = [], $data = [])
 * @method  $this event($event, $data = [])
 * @method  $this confirm(Actions $confirm, $msg = '', $title = '', $cancel = null)
 * @method  $this popConfirm(Actions $confirm, $content = '', $title = '', $cancel = null)
 * @method  $this popover($content, $title)
 * @method  $this openModal($content, $title) 异步弹窗
 * @method  $this modal(Element $component, $title = 'title') 同步弹窗
 * Class Custom
 * @package quick\components
 */
class QuickAction extends Element
{


    public $component = "quick-action";

    /**
     * @var
     */
    public $displayComponent;


    /**
     * @var Actions|string
     */
    public $action;

    /**
     * QuickAction constructor.
     * @param string|Element $name
     * @param string $type
     * @param string $size
     */
    public function __construct($name = '', string $type = '', string $size = '')
    {
        if ($name instanceof Element) {
            $this->displayComponent($name);
        } else {
            $name = $name ?: "action";
            $this->displayComponent(Component::button($name, $type, $size));
        }
        $this->message("动作未定义");

    }


    /**
     * 定义显示组件
     * @param Element|string $element
     * @return $this
     */
    public function displayComponent($element)
    {
        $this->displayComponent = $element;

        return $this;
    }


    /**
     * @param $element
     * @return $this|Element
     */
    public function content($element)
    {
        $this->displayComponent = $element;
        return $this;
    }


    /**
     *  设置动作
     * @param Actions $action
     * @return $this
     */
    public function action(Actions $action)
    {
        $this->action = $action;

        return $this;
    }


    /**
     * @param $name
     * @param mixed ...$arguments
     * @return $this
     */
    public function create($name, ...$arguments)
    {
        return $this->action(JsAction::{$name}(...$arguments));
    }


    /**
     * @param $method
     * @param $arguments
     * @return Element
     */
    public function __call($method, $arguments)
    {
        return $this->create($method, ...$arguments);
    }


    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $this->props([
            "action" => $this->action,
            'display' => $this->displayComponent
        ]);
        return array_merge(parent::jsonSerialize(), []);
    }
}
