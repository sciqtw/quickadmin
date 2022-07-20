<?php
declare (strict_types=1);

namespace quick\admin\actions\traits;


use app\admin\quick\actions\example\TestAction;
use quick\admin\components\Component;
use quick\admin\Element;
use quick\admin\http\response\actions\Actions;
use quick\admin\http\response\JsAction;

trait HandleActionTypeTrait
{

    /**
     * @var \Closure
     */
    protected $actionTypeCallback;


    /**
     * @param \Closure $closure
     * @return $this
     */
    public function setAction(\Closure $closure)
    {
        $this->actionTypeCallback = $closure;
        return $this;
    }


    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $data
     * @return $this
     */
    public function request($url, $method = 'get', $params = [], $data = [])
    {

        $this->setAction(function ($action) use ($url, $method, $params, $data) {
            $action->action = JsAction::request($url, $method, $params, $data);
        });
        return $this;
    }


    /**
     * @param string $path
     * @param array $query
     * @return $this
     */
    public function push(string $path, ?array $query = [])
    {

        $this->setAction(function ($action) use ($path, $query) {
            $action->action = JsAction::push($path, $query);
        });
        return $this;
    }


    /**
     * @param string $link
     * @param string $name
     * @return $this
     */
    public function download(string $link, string $name = '')
    {

        $this->setAction(function ($action) use ($link, $name) {
            $action->action = JsAction::download($link, $name);
        });
        return $this;
    }


    /**
     * @param string $url
     * @return $this
     */
    public function redirect(string $url)
    {

        $this->setAction(function ($action) use ($url) {
            $action->action = JsAction::redirect($url);
        });
        return $this;
    }


    /**
     *  新窗口打开链接
     *
     * @param string $url
     * @return $this
     */
    public function openInNewTab(string $url)
    {
        $this->setAction(function ($action) use ($url) {
            $action->action = JsAction::openInNewTab($url);
        });
        return $this;
    }


    /**
     * @param string $desc 弹窗说明
     * @param string $title 弹窗标题
     * @param string $cancelDesc 取消提示
     * @return $this
     */
    public function confirm(string $desc, string $title = '', string $cancelDesc = '')
    {
        $this->setAction(function ($action) use ($desc, $title, $cancelDesc) {

            $cancel = null;
            if (!empty($cancelDesc)) {
                $cancel = JsAction::message($cancelDesc, 'info');
            }
            $action->action = JsAction::confirmRequest($this->storeUrl(), $desc, $title, $cancel);
        });
        return $this;

    }


    /**
     * @param null|\Closure $dialog
     * @param string $url
     * @return $this
     */
    public function dialog($dialog = null, string $url = '')
    {

        $this->setAction(function ($action) use ($dialog, $url) {
            is_null($dialog) && $dialog = $action->name();
            $url = $url ? $url : $action->loadUrl();
            $action->action = JsAction::openModal($url, $dialog);
        });
        return $this;
    }


    /**
     * 设置动作为drawer类型
     *
     * @param null|\Closure $drawer
     * @param string $url
     * @return $this
     */
    public function drawer($drawer = null, string $url = '')
    {

        $this->setAction(function ($action) use ($drawer, $url) {
            is_null($drawer) && $drawer = $action->name();
            $url = $url ? $url : $this->loadUrl();
            $action->action = JsAction::openDrawer($url, $drawer);
        });
        return $this;
    }


    /**
     * @param string|\Closure $page
     * @param string $desc
     * @return $this
     */
    public function page($page = '', string $desc = '')
    {


        $title = $this->name();
        $content = Component::content();
        if ($page instanceof \Closure) {
            call_user_func($page, $this->panelComponent);
        } else {
            $title = empty($page) ? $this->name() : $page;
        }

        $content->children([
            Component::row()
                ->col(23, $title)
                ->col(1, Component::action("返回", '', 'default')->push($this->resource->createUrl('index'))
                )->slot('title')]);
        $desc && $content->description($desc);
        $this->panelComponent = $content;

        $this->setAction(function ($action) {
            $action->action = JsAction::push($action->loadUrl(), $action->urlParam);
        });


        return $this;
    }


    /**
     * @return Actions
     */
    protected function getAction()
    {
        if ($this->actionTypeCallback instanceof \Closure) {
            $callback = \Closure::bind($this->actionTypeCallback,$this);
            call_user_func($callback, $this);
        }
        if (!($this->action instanceof Actions)) {
            $this->action = [];//JsAction::push($this->loadUrl());
        }
        return $this->action;
    }


}
