<?php
declare(strict_types=1);

namespace components\admin\src\auth;


use quick\admin\actions\BatchDeleteAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\filter\Filter;
use quick\admin\Resource;
use quick\admin\table\Table;
use think\Request;

/**
 * 系统日志
 * @AdminAuth(title="系统日志",auth=true,menu=true,login=true)
 * @package app\admin\resource\auth
 */
class Oplog extends Resource
{
    /**
     * @var string
     */
    protected $title = '系统日志';

    /**
     * @var string
     */
    protected $description = "系统日志列表";

    /**
     * @var string
     */
    protected static $model = "app\\common\\model\\SystemOplog";


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["username"];


    public function model($model)
    {
        return $model->order("id desc");
    }

    /**
     * 过滤器
     * @param Request $request
     * @return array
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth(100);
        $filter->equal("username", "操作人")->width(12);

        return $filter;
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {
//        $table->props('height','700');
        $table->paginate(10);
        $table->disableFilter();
        $table->dataUsing(function ($data) {
            $ip = new \Ip2Region();
            $data = $data->toArray();
            foreach ($data as &$vo) {
                $isp = $ip->btreeSearch($vo['geoip']);
                $vo['isp'] = str_replace(['内网IP', '0', '|'], '', $isp['region'] ?? '');
            }
            return $data;
        });
        $table->attribute('border', false);
        $table->column("id", "ID")->width(80);
        $table->column("username", "操作人");
        $table->column("node", "操作权限");
        $table->column("geoip", "地理位置")->display(function ($value, $row, $v) {

            return Component::html($row['geoip'] . '<br>' . $row['isp']);
        });
        $table->column("created_at", "操作时间");

        $table->disableActions();


        return $table;
    }


    /**
     * 注册行操作
     * @return array|mixed
     */
    protected function actions()
    {
        return [];
    }


    /**
     * 注册批量操作
     * @return array
     */
    protected function batchActions()
    {
        return [
//            BatchDeleteAction::make("批量删除")
        ];
    }


    /**
     * @param $action
     * @param $request
     * @return mixed
     */
    protected function deleteAction($action, $request)
    {
        return false;
    }

    /**
     * @param \quick\actions\RowAction|\quick\admin\actions\Action $action
     * @param Request $request
     * @return bool|\quick\actions\RowAction
     */
    protected function editAction($action, $request)
    {
        return false;
    }


    /**
     * @param \quick\admin\actions\Action $action
     * @param Request $request
     * @return bool|\quick\admin\actions\Action
     */
    protected function addAction($action, $request)
    {
        return false;
    }


}
