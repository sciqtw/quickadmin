<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;



use think\facade\Db;
use think\helper\Str;

class CrudGenerator extends Generator
{

    public $table;
    public $namespace;
    public $create_model;
    public $create_lang;
    public $create_resource;
    public $force;
    public $fields;
    public $relations;
    public $resource_namespace;
    public $resource_name;


    public function create()
    {

        $service = new ModelsGenerator();
        $service->name = $this->name;
        $service->namespace = $this->namespace;
        $service->table = $this->table;
        $service->relations = $this->relations;
        $service->force = $this->force;


        $modelClass = $service->getModelClass();
        /** 创建模型 */
        if($this->create_model){

            $service->create(true);
        }

        /** 创建语言 */
        if($this->create_lang){

            $service->createLang(true);
        }

        /** 创建resource */
        if($this->create_resource){

            $resourceService = new BuildResource();
            $resourceService->name = $this->resource_name;
            $resourceService->force = $this->force;
            $resourceService->table = $this->table;
            $resourceService->namespace = $this->resource_namespace;
            $resourceService->modelClass = $service->getClassName($this->table);
            $resourceService->modelName = $service->getModelName($this->table);
            $resourceService->create($this->fields,true);
        }

    }
}
