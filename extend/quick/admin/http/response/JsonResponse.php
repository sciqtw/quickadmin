<?php
declare (strict_types=1);

namespace quick\admin\http\response;

use quick\admin\components\Component;
use quick\admin\components\element\Confirm;
use quick\admin\Element;
use quick\admin\http\response\actions\Actions;

class JsonResponse
{

    /**
     * @var
     */
    public $code;

    /**
     * @var
     */
    public $data = [];

    /**
     * @var
     */
    public $msg;

    /**
     * 前端动作类
     * @var array
     */
    protected $action = [];

    /**
     *
     * @var Confirm
     */
    protected $confirm;


    /**
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }


    /**
     * 动作失败
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return $this
     */
    public function error(string $msg = 'error', $data = [], int $code = 1)
    {
        $this->code = $code;
        $this->data = $data;
        $this->msg = $msg;
        return $this;
    }


    /**
     * 动作成功
     * @param string $msg
     * @param array $data
     * @param int $code
     * @return $this
     */
    public function success(string $msg = 'success', $data = [], int $code = 0)
    {
        $this->code = $code;
        $this->data = $data;
        $this->msg = $msg;

        return $this;
    }


    /**
     * 提示信息
     * @param string $msg 提示内容
     * @param string $type 动作完成 success/warning/info/error
     * @param bool $finish 动作完成
     * @return $this
     */
    public function message(string $msg, string $type = 'success', $finish = true)
    {
        $finish && $this->finish([], 300);
        return $this->addAction(JsAction::message($msg, $type));
    }


    /**
     * 警告
     * @param string $msg
     * @param bool $finish
     * @return $this
     */
    public function warning(string $msg, $finish = true)
    {
        $finish && $this->finish([], 300);
        return $this->addAction(JsAction::message($msg, 'warning'));
    }


    /**
     * 错误提示
     *
     * @param string $msg
     * @param bool $finish
     * @return $this
     */
    public function danger(string $msg, $finish = true)
    {
        $finish && $this->finish([], 300);
        return $this->addAction(JsAction::message($msg, 'error'));
    }


    /**
     * 路由页面
     *
     * @param string $push
     * @param int $delay
     * @return $this
     */
    public function push(string $push, $delay = 0)
    {
        return $this->addAction(JsAction::push($push)->delay($delay));
    }


    /**
     * 响应下载
     *
     * @param string $link
     * @param string $name
     * @return $this
     */
    public function download(string $link, string $name = '')
    {
        return $this->addAction(JsAction::download($link, $name = ''));
    }


    /**
     *  重定向
     * @param string $url
     * @return $this
     */
    public function redirect(string $url)
    {
        return $this->addAction(JsAction::redirect($url));
    }


    /**
     *  重定向到新选项卡
     * @param string $url
     * @return $this
     */
    public function openInNewTab(string $url)
    {
        return $this->addAction(JsAction::openInNewTab($url));
    }


    /**
     * @param string $url
     * @param string $desc
     * @param string $title
     * @return $this
     */
    public function confirmRequest(string $url, string $desc, string $title)
    {
        return $this->addAction(JsAction::confirmRequest($url, $desc, $title));
    }


    /**
     * @param Actions $confirm 确定
     * @param string $msg 提示内容
     * @param string $title 提示标题
     * @param null|Actions $cancel 取消
     * @param int $delay 响应执行延迟时间
     * @return $this
     */
    public function confirm(Actions $confirm, $msg = '', $title = '', $cancel = null, int $delay = 200)
    {

        return $this->addAction(JsAction::confirm($confirm, $msg, $title, $cancel)->delay($delay));
    }


    /**
     * 触发前端事件
     *
     * @param string $event
     * @param array $data
     * @param int $dalay
     * @param bool $isQuick
     * @return $this
     */
    public function event(string $event, array $data = [], $dalay = 0, bool $isQuick = false)
    {
        $action = JsAction::event($event, $data)->delay($dalay);
        if ($isQuick) {
            $action->isQuick();
        }
        return $this->addAction($action);
    }


    /**
     * 刷新页面数据
     * @param array $data
     * @param int $dalay
     * @return $this
     */
    public function refresh(array $data = [], $dalay = 0)
    {
        return $this->event('refresh', $data, $dalay, true);
    }


    /**
     * @param Element $component
     * @param string $title
     * @param null|\Closure $dialog
     * @return $this
     */
    public function modal(Element $component, $title = '')
    {

        return $this->addAction(JsAction::openModal($component, $title));
    }


    /**
     *  动作完成
     *
     * @param $data
     * @param int $dalay
     * @return $this
     */
    public function finish($data, $dalay = 0)
    {
        return $this->event('actionExecuted', $data, $dalay);
    }


    /**
     * @param Actions $action
     * @return $this
     */
    public function addAction(Actions $action)
    {
        $this->action = array_merge($this->action, [$action]);

        return $this;
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        if (!empty($this->action) && is_null($this->code)) {
            $this->success();
        }

        $jsonData = [
            'code' => $this->code,
            'msg' => $this->msg,
            'data' => $this->data
        ];

        !empty($this->action) && $jsonData['action'] = $this->action;

        return $jsonData;
    }


    /**
     * @return \think\response\Json
     */
    public function send()
    {
        return json($this->jsonSerialize());
    }


}
