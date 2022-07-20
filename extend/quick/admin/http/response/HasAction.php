<?php
declare (strict_types=1);

namespace quick\admin\http\response;


use quick\admin\Element;
use quick\admin\http\response\actions\Actions;
use quick\admin\http\response\actions\ConfirmAction;
use quick\admin\http\response\actions\DialogAction;
use quick\admin\http\response\actions\DownloadAction;
use quick\admin\http\response\actions\EventAction;
use quick\admin\http\response\actions\MessageAction;
use quick\admin\http\response\actions\ModalAction;
use quick\admin\http\response\actions\OpenInNewTabAction;
use quick\admin\http\response\actions\PopConfirmAction;
use quick\admin\http\response\actions\PopoverAction;
use quick\admin\http\response\actions\PushAction;
use quick\admin\http\response\actions\RedirectAction;
use quick\admin\http\response\actions\RequestAction;
use think\Exception;

/**
 * Class JsAction
 * @package quick\admin\http\response
 * @method  MessageAction message($msg, $type = 'success')
 * @method  PushAction push(string $path, array $query = [])
 * @method  DownloadAction download(string $link, string $name = '')
 * @method  RedirectAction redirect(string $url)
 * @method  OpenInNewTabAction openInNewTab(string $url)
 * @method  RequestAction request(string $url, $method = 'get', $params = [], $data = [])
 * @method  EventAction event($event, $data = [])
 * @method  ConfirmAction confirm(Actions $confirm, $msg = '', $title = '', $cancel = null)
 * @method  DialogAction openModal($content, $dialog = '')
 * @method  ModalAction modal(Element $component, $title = 'title')
 * @method  PopoverAction popover($content, $title = 'title')
 *
 */
trait HasAction
{


    /**
     * @var array
     */
    protected static $supports = [
        'message' => MessageAction::class,
        'push' => PushAction::class,
        'download' => DownloadAction::class,
        'redirect' => RedirectAction::class,
        'openInNewTab' => OpenInNewTabAction::class,
        'request' => RequestAction::class,
        'event' => EventAction::class,
        'confirm' => ConfirmAction::class,
        'popConfirm' => PopConfirmAction::class,
        'openModal' => DialogAction::class,
        'modal' => ModalAction::class,
        'popover' => PopoverAction::class,
    ];


    public static function openDrawer(string $url, $drawer)
    {
        return self::openModal($url, $drawer, 'drawer');
    }

    /**
     * @param $url
     * @param array $params
     * @param array $data
     * @return RequestAction
     */
    public static function post($url, $params = [], $data = [])
    {
        return self::request($url, 'POST', $params, $data);
    }


    /**
     * @param $url
     * @param array $params
     * @param array $data
     * @return RequestAction
     */
    public static function get($url, $params = [], $data = [])
    {
        return self::request($url, 'GET', $params);
    }


    /**
     * 前端弹出确认请求
     *
     * @param string $url 确认提交地址
     * @param string $msg 提示内容
     * @param string $title 提示标题
     * @param null $cancel 取消动作
     * @return ConfirmAction
     */
    public static function confirmRequest(string $url, $msg = '', $title = '', $cancel = null)
    {
        return self::confirm(RequestAction::make($url, 'POST'), $msg, $title, $cancel);
    }


    /**
     * @param $name
     * @return bool|mixed|string
     */
    public static function findComponentClass($name)
    {

        $class = static::$supports[$name] ?? '';
        if (!empty($class) && class_exists($class)) {
            return $class;
        }
        return false;
    }

    /**
     * @param $name
     * @param mixed ...$arguments
     * @return Actions
     * @throws Exception
     */
    public static function findAction($name, ...$arguments)
    {

        if ($className = static::findComponentClass($name)) {
            /** @var Actions $object */
            $object = new $className(...$arguments);
            return $object;
        }
        throw new Exception('找不到组件' . $name);
    }


    public static function __callStatic($method, $arguments)
    {
        return static::findAction($method, ...$arguments);
    }


}
