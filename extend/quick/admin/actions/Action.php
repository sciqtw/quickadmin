<?php
declare (strict_types=1);

namespace quick\admin\actions;


use Closure;
use quick\admin\actions\traits\HandleActionTypeTrait;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\contracts\ActionInterface;
use quick\admin\http\response\actions\Actions;
use quick\admin\Quick;
use quick\admin\Element;
use quick\admin\Resource;
use quick\admin\http\response\JsonResponse;
use quick\admin\http\response\ActionType;
use think\Exception;
use think\facade\Route;
use think\helper\Str;

/**
 * @AdminAuth(auth=true,menu=true,login=true,title="action")
 * @package Quick\Actions
 */
abstract class Action extends Element implements ActionInterface
{


    use HandleActionTypeTrait;


    /**
     * 动作组件
     *
     * @var string
     */
    public $component = 'quick-action';

    /**
     * 动作显示名称
     *
     * @var string
     */
    protected $name;

    /**
     *  定义前端动作类型
     * @var ActionType
     */
    protected $action;


    /**
     * 动作按钮展示组件
     *
     * @var Element
     */
    protected $display;

    /**
     * 动作容器组件
     * @var
     */
    protected $panelComponent;

    /**
     * @var \think\Request
     */
    protected $request;

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model;

    /**
     * 模型主键
     *
     * @var string
     */
    public static $keyName = "_keyValues_";

    /**
     * 模型主键
     *
     * @var string
     */
    public static $pk = "id";

    /**
     * 权限
     * @var
     */
    protected $canRunCallback;

    /**
     * 动作处理.
     *
     * @var \Closure
     */
    protected $beforeRenderCallback;

    /**
     *  响应
     * @var JsonResponse
     */
    protected $response;

    /**
     *  资源管理器
     *
     * @var Resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $urlParam = [];


    /**
     * @var array
     */
    protected $data = [];


    protected $uriKeyAppend;


    /**
     * Action constructor.
     * @param string $name
     * @param array $data
     */
    public function __construct(string $name = '', $data = [])
    {
        $name && $this->name = $name;

        is_array($data) && $this->data($data);
        $this->request = app()->request;
        $this->init();
    }


    /**
     * 初始化
     * @return $this
     */
    protected function init()
    {
        return $this;
    }


    /**
     * 定义动作
     *
     */
    protected function initAction()
    {

    }


    /**
     *  设置url参数
     * @param array $data
     * @return $this
     */
    public function param(array $data)
    {
        $this->urlParam = array_merge($this->urlParam, $data);
        return $this;
    }


    /**
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }


    /**
     * 动作名称
     * @return string
     */
    protected function name()
    {
        return $this->name ?: Quick::humanize($this);
    }


    /**
     *
     * @param string $appendKey
     * @return $this
     */
    public function setUriKeyAppend(string $appendKey)
    {
        $this->uriKeyAppend = $appendKey;
        return $this;
    }


    /**
     *  获取操作的uri
     *
     * @return string
     */
    public function uriKey()
    {
        $key = Str::snake(Quick::humanize(class_basename(get_called_class())));
        if ($this->uriKeyAppend) {
            $key = $key . '_' . $this->uriKeyAppend;
        }
        return $key;
    }


    /**
     * @return \quick\admin\components\element\ElButton|Element
     */
    protected function getDisplay()
    {
        if (!$this->display) {
            /** 设置默认组件 */
            $this->display = Component::button($this->name());
        }
        return $this->display;
    }


    /**
     *  设置动作显示组件
     *
     * @param null|Element|Closure $display
     * @return $this
     */
    public function display($display = null)
    {
        if ($display instanceof Element) {
            $this->display = $display;
        }
        if (empty($this->display)) {
            $this->display = Component::button($this->name());
        }

        if ($display instanceof Closure) {
            call_user_func($display, $this->display);
        }
        return $this;
    }

    /**
     * @param $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }


    /**
     * 获取数据模型
     *
     * @return mixed
     * @throws Exception
     */
    protected function getModel()
    {

        if (static::$model) {
            $model = new static::$model;
        } elseif ($this->resource instanceof Resource) {
            $model = $this->resource::newModel();
        }

        if (empty($model)) {
            throw new Exception("model 未定义");
        }
        return $model;
    }


    /**
     * 设置权限回调
     * @param Closure $closure
     * @return $this
     */
    public function canRun(Closure $closure)
    {
        $this->canRunCallback = $closure;
        return $this;
    }


    /**
     * 处理权限
     * @param $request
     * @param $model
     * @return bool|mixed
     */
    public function handleCanRun($request, $model)
    {
        if ($this->canRunCallback) {
            return call_user_func($this->canRunCallback, $request, $model);
        }
        return true;
    }


    /**
     * @return JsonResponse
     */
    public function response()
    {
        if (is_null($this->response)) {
            $this->response = JsonResponse::make();
        }
        return $this->response;
    }


    /**
     * 异步显示数据
     *
     * @return mixed
     */
    public function load()
    {

    }


    /**
     *  数据持久化
     *
     * @return mixed
     */
    public function store()
    {

    }


    /**
     * 动作数据提交接口
     *
     * @param array $param
     * @return string
     */
    public function storeUrl(array $param = [])
    {
        return $this->createUrl('store', $param);
    }


    /**
     * 动作数据展示接口
     *
     * @param array $param
     * @return string
     */
    public function loadUrl(array $param = [])
    {
        return $this->createUrl('load', $param);
    }


    /**
     * 生成当前动作访问地址
     *
     * @param string $methods
     * @param array $param
     * @return string
     */
    public function createUrl(string $methods, array $param = [])
    {
        $module = Route::buildUrl()->getAppName();
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();

        if (!empty($param) || !empty($this->urlParam)) {
            $param = http_build_query(array_merge($this->urlParam, $param));
            $param = strpos('?', $methods) === false ? '?' . $param : "&" . $param;
        } else {
            $param = '';
        }

        return "/{$module}/resource/{$resource}/{$uriKey}/{$methods}" . $param;
    }


    /**
     *  生成当前动作访问地址
     *
     * @param string $methods
     * @return string
     */
    public function backUrl(string $methods)
    {
        $module = Route::buildUrl()->getAppName();
        $resource = app()->request->route('resource');
        return "/{$module}/resource/{$resource}/{$methods}";
    }


    /**
     * @param Element $component
     * @return Element
     */
    protected function resolveComponent(Element $component)
    {
        if ($this->panelComponent) {
            $this->panelComponent->children($component);
            $component = $this->panelComponent;
        }
        return $component;
    }


    /**
     * 定义动作处理逻辑回调
     * @param callable $beforeRenderCallback
     * @return $this
     */
    public function beforeRenderUsing(callable $beforeRenderCallback)
    {
        $this->beforeRenderCallback = $beforeRenderCallback;

        return $this;
    }


    /**
     *  注册动作类  resource查找发现
     * @return array
     */
    public function getChildrenComponents()
    {
        return array_merge(parent::getChildrenComponents(), [$this->getDisplay()]);
    }


    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $this->initAction();
        if ($this->beforeRenderCallback instanceof Closure) {
            call_user_func($this->beforeRenderCallback, $this);
        }

        $this->props([
            "action" => $this->getAction(),
            "display" => $this->getDisplay(),
        ]);
        return array_merge(parent::jsonSerialize(), [
            'uriKey' => $this->uriKey(),
        ]);
    }

}
