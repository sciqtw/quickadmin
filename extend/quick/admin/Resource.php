<?php
declare (strict_types=1);

namespace quick\admin;


use phpDocumentor\Reflection\Types\Boolean;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\contracts\ActionInterface;
use quick\admin\http\response\JsonResponse;
use think\exception\HttpException;
use quick\admin\filter\Filter;
use quick\admin\table\Table;
use quick\admin\form\Form;
use think\facade\Route;
use think\helper\Str;
use think\Exception;
use think\App;
use think\Model;
use think\Request;

abstract class Resource
{


    use ResolvesTable,
        ResolvesActions;

    /**
     * 标题
     *
     * @var string
     */
    protected $title;


    /**
     * @var string
     */
    protected $description;

    /**
     * 主键名称
     *
     * @var string
     */
    public $keyName = 'id';

    /**
     * @var Request
     */
    protected $request;

    /**
     * 快捷搜索字段
     * @var
     */
    protected static $search;

    /**
     * @var App
     */
    protected $app;


    protected static $acitonClass = [

    ];


    /**
     * Resource constructor.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $app->request;

        $this->init();
    }


    /**
     *
     */
    protected function init()
    {

    }


    /**
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }


    /**
     * @return string
     */
    protected function description()
    {
        return $this->description;
    }


    /**
     * 资源的URI密钥
     *
     * @return string
     */
    public static function uriKey()
    {
        return Str::snake(class_basename(get_called_class()));
    }


    /**
     * 获取由资源表示的模型的新实例
     *
     * @return bool|Model
     */
    public static function newModel()
    {
        $model = static::$model;
        if (empty($model)) {
            return false;
        }
        return new $model;
    }


    /**
     * 注册行操作
     * @return mixed
     */
    abstract protected function actions();


    /**
     * 注册批量操作
     *
     * @return mixed
     */
    abstract protected function batchActions();


    /**
     * @param Table $table
     * @return Table
     */
    protected function table(Table $table)
    {
        return $table;
    }


    /**
     * @param Form $form
     * @param Request $request
     * @return bool
     */
    protected function form(Form $form, Request $request)
    {
        return $form;
    }


    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {
        return $filter;
    }

    /**
     * @return Filter|Boolean
     */
    protected function getFilter()
    {
        $filter = Filter::make();
        return $this->filters($filter);
    }


    // 旧方案 准备删除 todo
//    /**
//     * 过滤器
//     * @AdminAuth(title="过滤器",auth=false,menu=false,login=true)
//     * @return \think\response\Json
//     */
//    public function filter()
//    {
//        return $this->success('success', $this->getFilter());
//    }


    /**
     * 资源管理器
     *
     * @AdminAuth(title="列表",auth=true,menu=true,login=true)
     * @param Content $content
     * @return \think\response\Json
     * @throws Exception
     */
    public function index(Content $content)
    {

        if($this->request->isPost()){
            return $this->success('success', $this->getTable()->buildData());
        }
        return $this->success('success', $this->display());
    }


    /**
     * display
     *
     * @return Content
     * @throws Exception
     */
    protected function display()
    {

        $content = Component::content();
        $content->title($this->title())
            ->description($this->description())
            ->body($this->getTable()->displayRender());
        return $content;
    }



    /**
     * 返回失败的操作
     *
     * @param mixed $msg 消息内容
     * @param mixed $data 返回数据
     * @param integer $code 返回代码
     * @return \think\response\Json
     */
    protected function error($msg, $data = [], $code = 1)
    {

        return JsonResponse::make()->error($msg, $data, $code)->send();
    }


    /**
     * @param $msg
     * @param array $data
     * @param int $code
     * @return \think\response\Json
     */
    protected function success($msg, $data = [], $code = 0)
    {
        return JsonResponse::make()->success($msg, $data, $code)->send();
    }


    /**
     * @param string $methods
     * @param array $param
     * @return string
     */
    public function createUrl(string $methods,array $param = [])
    {
        $module = Route::buildUrl()->getAppName();
        $resource = app()->request->route('resource');
        if (!empty($param) ) {
            $param = http_build_query($param);
            $param = strpos('?', $methods) === false ? '?' . $param : "&" . $param;
        } else {
            $param = '';
        }
        return "/{$module}/resource/{$resource}/{$methods}". $param;
    }



    /**
     * 查找资源action class
     *
     * @param string $name
     * @return bool|mixed
     * @throws Exception
     */
    protected function findActionClass(string $name)
    {

        $actions = $this->getAllActions();
        if (isset($actions[$name])) {
            $action = $actions[$name];
            if (is_callable([$action, 'setResource'])) {
                $action->setResource($this);
            }
            return $action;
        }
        return false;
    }


    /**
     *  注册action
     *
     * @return array
     */
    protected function actionsClass():array
    {
        return [];
    }



    /**
     * 获取当前资源的所有动作类
     *
     * @return array
     * @throws Exception
     */
    public function getAllActions()
    {
        // 当前资源类所有使用到的组件必须在此方法返回，否则注册的动作类无法访问
        $batchActions = $this->getBatchActions();
        $table = $this->getTable();
        $classList = $this->actionsClass();

        $actions = array_merge($batchActions, [$table, $this->display()],$classList);
        return $this->_getActionsList($actions);
    }


    /**
     *  把组件内的动作类全部取出来
     *
     * @param array $actions
     * @return array
     */
    protected function _getActionsList(array $actions)
    {
        $list = [];
        foreach ($actions as $action) {

            if (($action instanceof Element) && $childrenComponents = $action->getChildrenComponents()) {
                $childrenComponents = is_array($childrenComponents) ? $childrenComponents:[$childrenComponents];
                if ($actionList = $this->_getActionsList($childrenComponents)) {
                    $list = array_merge($list, $actionList);
                }
            }

            if (is_callable([$action, "uriKey"]) && $action instanceof ActionInterface) {
                $list[$action->uriKey()] = $action;
            }
        }
        return $list;
    }


    /**
     * @param string $actionUriKey
     * @param string $func
     * @return mixed
     * @throws Exception
     */
    public function handleResourceAction(string $actionUriKey, string $func)
    {

        if ($actionInstance = $this->findActionClass($actionUriKey)) {

            return $actionInstance;

        }
        // 操作不存在
        throw new HttpException(404, lang('resource action %s not found', [get_class($this) . '->' . $actionUriKey . '()']));

    }


}
