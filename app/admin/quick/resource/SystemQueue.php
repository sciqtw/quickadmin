<?php

namespace app\admin\quick\resource;


use app\admin\quick\actions\QueueLogAction;
use app\admin\quick\actions\ReQueueAction;
use app\admin\quick\actions\example\ReplicateAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Content;
use quick\admin\components\layout\Row;
use quick\admin\filter\Filter;
use quick\admin\library\service\AuthService;
use quick\admin\library\service\ProcessService;
use quick\admin\table\Table;
use think\Request;


/**
 * 系统任务
 * @AdminAuth(auth=true,menu=true,login=true,title="系统任务")
 * Class SystemConfig
 * @package app\admin\quick\resource
 */
class SystemQueue extends Resource
{
    /**
     * 标题
     *
     * @var string
     */
    protected $title = '系统任务';

    /**
     * @var string
     */
    protected $description = "系统任务";

    /**
     * @var string
     */
    protected static $model = 'quick\admin\http\model\SystemQueue';


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = [];



    protected function display()
    {
        $command = '';
        // 超级管理面板
        if (AuthService::instance()->isSuper() && app()->isDebug()) {
            $process = ProcessService::instance();
            if ($process->iswin() || empty($_SERVER['USER'])) {
                $command = $process->think('quick:queue start');
            } else {
                $command = "sudo -u {$_SERVER['USER']} {$process->think('quick:queue start')}";
            }
        }

        $runStatus = Component::custom('el-link')->props([
            'type' => 'warning',
            'underline' => false,
        ])->content('只有超级管理员才能操作！');
        if (AuthService::instance()->isSuper() && app()->isDebug()) {
            try {
                $message = $this->app->console->call('quick:queue', ['status'])->fetch();
                if (preg_match('/process.*?\d+.*?running/', $message, $attrs)) {
                    $runStatus->content($message)->props('type', 'success');
                } else {
                    $runStatus->content($message)->props('type', 'danger');
                }
            } catch (\Exception $exception) {
                $runStatus->content($exception->getMessage())->props('type', 'danger');
            }
        }


        $content = Component::content();
        $content->title($this->title())
            ->children(Component::custom('div')->style([
                'height' => '70px',
                'font-size' => '13px',
                'color' => '#999',
                'padding-bottom' => '20px',
            ])->children(Component::row()->col(12, [
                Component::custom('div')->style([
                    'color' => '#999',
                    'padding' => '10px 0',
                ])->content('后台服务主进程运行状态'),
                $runStatus,
            ])->col(12, [
                Component::custom('div')->style([
                    'color' => '#999',
                    'padding' => '10px 0',
                ])->content('配置定时任务来检查并启动进程（建议每分钟执行）'),
                Component::custom('div')->style([
                    'color' => '#999',
                ])->content($command)
            ])), 'description')
            ->body(function (Row $row) {
                $row->attribute('gutter', 10);
                $gari = [
                    "xs" => 24,
                    "sm" => 12,
                    "md" => 8,
                    "lg" => 8,
                ];
                $row->col(24, $this->getTable()->displayRender());

            });
        return $content;
    }

    /**
     * @param Filter $filter
     * @return Filter
     */
    protected function filters(Filter $filter)
    {

        $filter->labelWidth("100");
        $filter->like("title", "任务名称")->width(12);

        return $filter;
    }

    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {


        $table->attribute('border', true);
        $table->attribute('size', 'small');
        $table->attribute('show-overflow-tooltip', true);

        $table->column("code", "任务名称")->sortable()->display(function ($value,$row){
            return Component::html("<div>任务编号：{$value}</div><div>任务名称：{$row['title']}</div>");
        })->width(230);

        $table->column("command", "任务计划")->display(function ($value,$row){
            $str = empty($row['rscript']) ? "单次任务":"每".$row['loops_time']."秒";
            $time =  date("Y-m-d H:i:s",$row['available_time']);
            return Component::html("<div>执行Job：{$value}</div><div>计划执行：{$time} <span style='color:#4392ff'>({$str})</span> </div>");
        })->width(350);


        $table->column("desc", "执行状态")->display(function ($value,$data) {
            $data['enter_time'] = $data['enter_time'] ?? '';
            $data['outer_time'] = $data['outer_time'] ?? '0.0000';
            if (!empty($data['reserve_time'])) {
                $num = round($data['outer_time'] - $data['enter_time'], 4);
                $runRes =  date("H:i:s", substr($data['enter_time'], 0, 10)) . "( 耗时 " . $num . " 秒)";
            } else {
                $runRes =  '任务未执行';
            }
            $value = $value ? $value:'未获取到执行结果';

            return Component::html("<div>执行时间：{$runRes} 已执行<span style='color:#4392ff'>({$data['attempts']})</span>次 </div><div>执行结果：{$value}</div>");
        });
        $table->column("status", "状态")->display(function ($value,$row){
            $str = $row['rscript'] == 1 ? "待执行": "未执行";
            if($value == 1){
                $this->label("info");

            }elseif($value == 2){
                $this->label("success");
                $str = "处理中";
            }elseif($value == 3){
                $this->label('success');
                $str = "执行成功";
            }elseif($value == 4){
                $str = "执行失败";
                $this->label("danger");
            }

            return $str;
        })->width(100);

        $table->column("create_time", "创建时间")->width(110);


        return $table;
    }

    /**
     * 注册行操作
     * @return array|mixed
     */
    protected function actions()
    {
        return [
            ReQueueAction::make("重置任务")->canRun(function ($request, $model) {
                return $model['rscript'] == 0;
            }),
            QueueLogAction::make("查看")
        ];
    }


    /**
     * @param $action
     * @param $request
     * @return mixed
     */
    protected function deleteAction($action, $request)
    {
        return $action;
    }


}
