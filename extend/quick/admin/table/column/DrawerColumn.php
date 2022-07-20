<?php
declare (strict_types=1);

namespace quick\admin\table\column;


use Closure;

use quick\admin\components\Component;
use quick\admin\contracts\ActionInterface;
use quick\admin\contracts\Renderable;
use quick\admin\http\response\JsonResponse;

class DrawerColumn extends AbstractColumn implements ActionInterface
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'index-text-field';


    /**
     *
     * @var
     */
    protected $drawer;


    /**
     * @param null $callback
     * @return mixed|\quick\admin\components\element\QuickDialog
     */
    public function init($callback = null)
    {
        // 初始化 dialog
        $drawer = Component::drawer()->attribute([
            "append-to-body" => true,
        ])->size('50%');
        $this->drawer = $drawer;


        if (func_num_args() == 2) {
            list($title, $callback) = func_get_args();
            if ($title instanceof Closure) {
                call_user_func($title, $this->drawer);
            } else {
                $drawer->attribute('title', $title);
            }
        } elseif (func_num_args() == 1) {
            $drawer->attribute('title', __("title"));
        }


        if (is_subclass_of($callback, Renderable::class)) {
            $this->asyncComponent = $callback;
        }

        //设置图标
        $this->prefix(Component::icon('folder')->color("#409EFF"));


        if ($callback instanceof Closure) {
            $this->displayUsing(function ($value, $row, $originalValue) use ($callback) {
                $data = call_user_func($callback, $this->drawer, $value, $row, $originalValue);
                if ($data) {
                    $this->drawer->setChildren([$data]);
                }
            });
        }
        return $drawer;
    }

    /**
     *  异步数据接口
     *
     * @return \think\response\Json
     */
    public function load()
    {

        if ($asyncComponent = $this->asyncComponent) {
            /** @var Renderable $object */
            $object = new $asyncComponent();
            $res = $object->render();
            $this->drawer->children($res);
            return JsonResponse::make()->success('success', $this->drawer)->send();
        }
        return JsonResponse::make()->error('error')->send();;

    }

    public function store()
    {
        // TODO: Implement update() method.
    }

    /**
     * @return mixed|string
     */
    public function loadUrl()
    {
        $module = str_replace('.', '/', app('http')->getName());
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();
        $fun = "show";
        return "{$module}/resource/{$resource}/{$uriKey}/{$fun}";
    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $this->callDisplayCallback();

        return array_merge(parent::jsonSerialize(), [
            'container' => $this->drawer,
        ]);

    }

}
