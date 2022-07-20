<?php
declare (strict_types=1);

namespace quick\admin\table\action;


use quick\admin\contracts\ActionInterface;
use quick\admin\form\Form;
use quick\admin\http\response\JsonResponse;
use quick\admin\table\column\AbstractColumn;

abstract class ActionColumn extends AbstractColumn implements ActionInterface
{
    /**
     * The column's component.
     *
     * @var string
     */
    public $component = 'index-text-field';

    /**
     * @param string $rule
     * @return $this|mixed
     */
    public function init($rule = '')
    {

        return $this;
    }



    /**
     * 异步数据接口
     *
     */
    public function resolve()
    {
        $form = Form::make('d')->slot("popover");
        $form->text('d','d');
        $form->text('d','d');
        $form->text('d','d');

        return JsonResponse::make()->success("修改成功",$form)->send();;
    }


    /**
     * @return \think\response\Json
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle()
    {
        $value = request()->param(static::$colValue);
        $pkValue = request()->param(static::$keyValue);
        $model = $this->resource::newModel()->where($this->table->getKey(),$pkValue)->find();
        if (!$model) {
            quick_abort(500, '资源不存在');
        }

        $model->{$this->name} = $value;
        $res = $model->save();
        if($res !== false){
            return JsonResponse::make()->success("修改成功")->send();
        }else{
            return JsonResponse::make()->error("修改失败")->send();
        }


    }



    public function store()
    {
        return $this->handle();
    }


    /**
     * @return mixed|string
     */
    public function loadUrl()
    {
        return $this->_actionUrl("load");
    }

    /**
     * @return mixed|string
     */
    public function storeUrl()
    {
        return $this->_actionUrl("store");
    }

    private function _actionUrl(string $func)
    {
        $module = str_replace('.', '/', app('http')->getName());
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();
        return "{$module}/resource/{$resource}/{$uriKey}/{$func}";
    }


}
