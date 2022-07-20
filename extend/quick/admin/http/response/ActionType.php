<?php
declare (strict_types=1);

namespace quick\admin\http\response;


use JsonSerializable;
use quick\admin\components\Component;
use quick\admin\components\element\Confirm;

class ActionType implements JsonSerializable
{

    /**
     * @var string
     */
    protected $type;

    /**
     * 动作组件 this.$emit('actionExecuted')
     * @var bool|int
     */
    protected $finish = false;

    /**
     * @var array
     */
    protected $action = [];

    /**
     *
     * @var Confirm
     */
    protected $confirm;


    /**
     * ActionType constructor.
     * @param string $msg
     */
    public function __construct($msg = '')
    {
        !empty($msg) && $this->message($msg);
    }


    /**
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }


    /**
     * @param $path
     * @param array $query
     * @return $this
     */
    public function push($path, $query = [])
    {
        return $this->actionResponse(__FUNCTION__, [
            'push' => [
                'path' => $path,
                'query' => $query,
            ],
        ]);
    }


    /**
     * @param string $message
     * @param string $type
     * @param bool $refresh
     * @return $this
     */
    public function message(string $message, string $type = 'success', $refresh = true)
    {
        $action = [ 'message' => $message, 'type' => $type];
        $refresh && $this->finish();
        $this->actionResponse(__FUNCTION__, $action);
        return $this;
    }


    /**
     *
     * @param string $message 提示信息
     * @param bool $refresh 是否刷新  动作组件 this.$emit('actionExecuted')
     * @return $this
     */
    public function danger(string $message, $refresh = false)
    {
        return $this->message($message, "warning", $refresh);
    }


    /**
     *
     * @param string $message 提示信息
     * @param bool|int $refresh 是否刷新 动作组件 this.$emit('actionExecuted')
     * @return $this
     */
    public function success(string $message, $refresh = true)
    {
        $this->message($message, "success", $refresh);

        return $this;
    }


    /**
     * Return a redirect response from the action.
     *
     * @param string $url
     * @return array
     */
    public function redirect($url)
    {
        return $this->actionResponse(__FUNCTION__, ['redirect' => $url]);
    }


    /**
     * @param $url
     * @return $this
     */
    public function openInNewTab($url)
    {
        return $this->actionResponse(__FUNCTION__, ['openInNewTab' => $url]);
    }


    /**
     * @param $url
     * @param $name
     * @return $this
     */
    public function download($url, $name)
    {
        return $this->actionResponse(__FUNCTION__, ['download' => $url, 'name' => $name]);
    }


    /**
     * @return $this
     */
    public function refresh()
    {
        return $this->actionResponse(__FUNCTION__, ['refresh' => true]);
    }


    /**
     * 动作组件 this.$emit('actionExecuted')
     *
     * @param int $delay 延迟执行时间 毫秒
     * @return $this
     */
    public function finish(int $delay = 0)
    {
        $this->finish = $delay;
        return $this;
    }


    /**
     * @param \Closure $confirm
     * @param \Closure|null $cancel
     * @param $msg
     * @param string $title
     * @return $this
     * @throws \Exception
     */
    public function confirm(\Closure $confirm, $cancel = null, $msg, $title = '')
    {
        if (!$this->confirm) {
            $confirm = call_user_func($confirm, ActionType::make());
            if (!($confirm instanceof ActionType)) {
                throw new \Exception("confirm is not instanceof ActionResponse ");
            }

            if ($cancel instanceof ActionType) {
                $cancel = call_user_func($cancel, ActionType::make());
            }

            $this->confirm = Component::confirm((is_string($msg) ? $msg : ''), $confirm, $cancel, $title);
        }
        if ($msg instanceof \Closure) {
            call_user_func($msg, $this->confirm);
        }
        return $this->actionResponse(__FUNCTION__, $this->confirm);
    }


    /**
     * @param $fieldsUrl
     * @param $submitUrl
     * @return $this
     */
    public function dialog($fieldsUrl, $submitUrl)
    {
        return $this->actionResponse(__FUNCTION__, [
            "fieldsUrl" => $fieldsUrl,
            "submitUrl" => $submitUrl,
        ]);
    }


    /**
     * @param $url
     * @param array $params
     * @param array $data
     * @return $this
     */
    public function getRequest($url, $params = [], $data = [])
    {
        return $this->_request($url, "GET", $params, $data);
    }


    /**
     * @param $url
     * @param array $params
     * @param array $data
     * @return $this
     */
    public function postRequest($url, $params = [], $data = [])
    {
        return $this->_request($url, "POST", $params, $data);
    }


    /**
     * @param $url
     * @param string $method
     * @param array $params
     * @param array $data
     * @return $this
     */
    private function _request($url, $method = "POST", $params = [], $data = [])
    {
        return $this->actionResponse("request", compact("url", "method", "params", "data"));
    }


    /**
     * @param $type
     * @param $action
     * @return $this
     */
    protected function actionResponse($type, $action)
    {
        $this->type = $type;
        $this->action = $action;
        return $this;
    }


    /**
     * @return array
     */
    public function getAction()
    {
        if (empty($this->action)) {
            $this->message("success");
        }
        return [
            'type' => $this->type,
            'finish' => $this->finish,
            'data' => $this->action,
        ];
    }


    /**
     * Prepare the action for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->getAction();
    }

}
