<?php

declare(strict_types=1);

namespace plugins\crud\controller;



use app\common\controller\Backend;
use app\common\service\admin\AttachmentCateEditService;
use app\common\service\common\BuildGroupViewService;
use app\Request;
use plugins\crud\command\service\BuildResource;
use plugins\crud\command\service\BuildTable;
use plugins\crud\command\service\CrudConfig;
use plugins\crud\command\service\CrudGenerator;
use plugins\crud\command\service\ModelsGenerator;
use plugins\crud\command\service\ParseModel;
use plugins\crud\command\service\BuildForm;
use plugins\crud\command\service\ServiceGenerator;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use think\facade\Db;
use think\helper\Str;

/**
 * Class Index
 * @AdminAuth(
 *     title="crud",
 *     auth=true,
 *     menu=true,
 *     login=false
 * )
 * @package app\admin\controller
 */
class Index extends Backend
{


    /**
     * @AdminAuth(title="编辑分类",auth=true,menu=true,login=true)
     * @param Request $request
     * @return \think\response\Json
     */
    public function tableFields(Request $request)
    {
        $table = $request->post('table');
        $isRelation = $request->post('relation');
        $data = ParseModel::getTableFields($table);
        $data = array_values($data);
        $prefix = Db::table($table)->newQuery()->getConfig('prefix');

        foreach ($data as &$item){
            if($isRelation){
                $relationName = stripos($table, $prefix) === 0 ? substr($table, strlen($prefix)) : $table;
                $item['relation'] = $relationName;
            }else{
                $item['relation'] = false;
            }

            $item['rule'] = implode("|",$item['rule']);
        }

        $ttt = CrudConfig::formItem();
        return json([
            'code' => 0,
            'type' => $ttt,
            'data' => $data,
        ]);
    }



    public function tableList()
    {

        $list = [];
        $data = Db::query("SHOW TABLES");
        foreach ($data as $key => $row) {
            $list[reset($row)] = reset($row);
        }
        return json([
            'code' => 0,
            'data' => $list,
        ]);
    }


    public function previewForm(Request $request)
    {

        $fields = json_decode($request->post('fields',[]),true);
        $form = BuildForm::buildField($fields,'');
        return json([
            'code' => 0,
            'data' => $form,
            'fields' => $fields,
        ]);
    }


    public function previewModel(Request $request)
    {
        $fields = json_decode($request->post('fields',[]),true);
        $name = $request->post('name');
        $namespace = $request->post('namespace');
        $service = new ModelsGenerator();
        $service->name = $name;
        $service->namespace = $namespace;
        $service->table = $request->post('table');
        $service->fields = $fields;
        $service->relations = json_decode($request->post('relation',[]),true);
        if($request->post('show_type') == 'lang'){
            $res = $service->createLang();
        }else{
            $res = $service->create();
        }

        return json([
            'code' => 0,
            'data' => Component::custom('qk-code')->props('data', $res),
        ]);
    }




    public function previewTable(Request $request)
    {

        $fields = json_decode($request->post('fields',[]),true);
        $form = BuildTable::buildField($fields,'');
        return json([
            'code' => 0,
            'data' => $form,
            'fields' => $fields,
        ]);
    }


    /**
     * @param Request $request
     * @return \think\response\Json
     * @throws \Exception
     */
    public function previewResource(Request $request)
    {

        $name = $request->post('name');
        $namespace = $request->post('namespace');
        $service = new ModelsGenerator();
        $service->name = $name;
        $service->namespace = $namespace;
        $service->table = $request->post('table');

        $fields = json_decode($request->post('fields',[]),true);
        $form = new BuildResource();
        $form->name =  $request->post('resource_name','');
        $form->namespace =  $request->post('resource_namespace','');
        $form->table =  $request->post('table','');
        $form->modelClass =  $service->getClassName($service->table);
        $form->modelName =  $service->getModelName($service->table);
        $res = $form->create($fields);
        return json([
            'code' => 0,
            'data' =>  Component::custom('qk-code')->props('data', $res),
        ]);
    }


    /**
     * 创建crud
     * @param Request $request
     * @return \think\response\Json
     */
    public function createCrud(Request $request)
    {
        $service = new CrudGenerator();
        $service->name = $request->post('name');;
        $service->create_model = $request->post('create_model');
        $service->create_resource = $request->post('create_resource');
        $service->create_lang = $request->post('create_lang');
        $service->namespace =  $request->post('namespace','');
        $service->resource_namespace =  $request->post('resource_namespace','');
        $service->resource_name =  $request->post('resource_name','');
        $service->table = $request->post('table');
        $service->relations = json_decode($request->post('relations',[]),true);
        $service->fields = json_decode($request->post('fields',[]),true);
        $service->force = $request->post('force',false);
        $res = $service->create();
        return json([
            'code' => 0,
            'data' => Component::custom('qk-code')->props('data', $res),
        ]);
    }



    public function previewAction(Request $request)
    {
        $fields = $request->post('fields');
        $name = $request->post('name');
        $namespace = $request->post('namespace');

        try {
            $service = new ServiceGenerator();
            $service->fields = $fields;
            $service->name = $name;
            $service->namespace = $namespace;
            $res = $service->create(false);
        }catch (\Exception $e){
            return json([
                'code' => 1,
                'msg' => $e->getMessage(),
            ]);
        }

        return json([
            'code' => 0,
            'data' => Component::custom('qk-code')->props('data', $res),
        ]);

    }


    public function createAction(Request $request)
    {
        $fields = $request->post('fields');
        $name = $request->post('name');
        $namespace = $request->post('namespace');
        $force = $request->post('force');

        try {
            $service = new ServiceGenerator();
            $service->fields = $fields;
            $service->name = $name;
            $service->namespace = $namespace;
            $service->force = $force;
            $service->create(true);
        }catch (\Exception $e){
            return json([
                'code' => 1,
                'msg' => $e->getMessage(),
            ]);
        }

        return json([
            'code' => 0,
            'msg' => '创建成功',
        ]);

    }


    public function createApi(Request $request)
    {
        $fields = $request->post('fields');
        $name = $request->post('name');
        $namespace = $request->post('namespace');
        $force = $request->post('force');

        try {
            $service = new ServiceGenerator();
            $service->fields = $fields;
            $service->name = $name;
            $service->namespace = $namespace;
            $service->force = $force;
            $service->create(true);
        }catch (\Exception $e){
            return json([
                'code' => 1,
                'msg' => $e->getMessage(),
            ]);
        }

        return json([
            'code' => 0,
            'msg' => '创建成功',
        ]);

    }

    public function test()
    {
        halt(Str::endsWith('dfdfdfd_att',['att2','tt']));
    }
}
