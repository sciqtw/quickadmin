<?php
declare (strict_types = 1);

namespace quick\admin\http;


use quick\admin\actions\Action;
use quick\admin\Quick;
use quick\admin\Resource;
use think\Model;

trait InteractsWithResources
{
    /**
     * 资源权限控制
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }


    /**
     * 获取资源类名
     * @return mixed
     */
    public function resource()
    {

        list($modelu,$resource) = [app('http')->getName(),app()->request->route('resource')];
        return tap(Quick::resourceForKey($modelu,$resource), function ($resource) {
            if (is_null($resource)) {
                abort(404, __("Resource class not found"));
            }
        });
    }

    /**
     * 获取资源类名
     * @return mixed
     */
    public function actions()
    {

        list($modelu,$actions) = [app('http')->getName(),app()->request->route('actions')];
        return tap(Quick::actionForKey($actions), function ($resource) {
            if (is_null($resource)) {
                abort(404, __("Resource class not found"));
            }
        });
    }

    /**
     * @return Resource
     */
    public function newResource(): Resource
    {
        $resource = $this->resource();

        return invoke($resource);
    }

    /**
     * @return Action
     */
    public function newAction(): Action
    {
        $resource = $this->actions();

        return invoke($resource);
    }


    /**
     * @return Model
     */
    public function model(): Model
    {
        $resource = $this->resource();

        return $resource::newModel();
    }
}
