<?php

namespace app\admin\quick\resource;


use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\components\layout\Row;
use quick\admin\components\QkTimeline;
use quick\admin\Element;

/**
 * @AdminAuth(auth=true,menu=true,login=true,title="系统首页")
 * @package app\admin\quick\resource
 */
class Dashboard extends Resource
{
    /**
     * 标题
     *
     * @var string
     */
    protected $title = '工作台';

    /**
     * @var string
     */
    protected $description = "早安，Admin，开始您一天的工作吧！";


    /**
     * @return Element
     */
    protected function display(): Element
    {

        $da = Component::custom('q-chart')->props('options', [
//                    'title' => ['text' => 'ECharts 入门示例'],
            'grid' => [
                'top' => "20px",
                'bottom' => "2px",
                'right' => "0px",
                'containLabel' => true
            ],
            'legend' => ['data' => ["用户", '浏览']],
            'tooltip' => [],
            'xAxis' => ['data' => ["3-02", "3-03", "3-04", "3-05", "3-06", "3-07"]],
            'yAxis' => [
//                        'data' => []
                []
            ],
            'series' => [
                [
                    'name' => '用户',
                    'type' => 'line',
                    'stack' => 'x',
                    'areaStyle' => [],
                    'data' => [5, 20, 36, 10, 10, 20],
                ],
                [
                    'name' => '浏览',
                    'type' => 'line',
                    'stack' => 'x',
                    'areaStyle' => [],
                    'data' => [5, 220, 36, 10, 10, 20],
                ]
            ],
        ])->props([
            'height' => '200px',
//            'w' => '200px',
        ]);

        $table = Component::qkDescriptions()->data([
            ['label' => 'php版本', 'content' => '7.4'],
            ['label' => 'element-plus', 'content' => '2.1.4'],
            ['label' => 'ThinkPHP', 'content' => '6'],
            ['label' => 'vue', 'content' => '3.0'],
            ['label' => 'QuickAdmin', 'content' => '1.0.0.dev'],
        ])->border()->column(1);

        $content = Component::content();
        $content->title($this->title())
            ->description($this->description())
            ->body(function (Row $row) use ($da, $table) {
                $row->props('gutter', 15);


                $row->col([
                    "xs" => 12,
                    "sm" => 12,
                    "md" => 12,
                    "lg" => 12,
                ], Component::card($da)->header(Component::custom('div')->children([
                    Component::icon('el-icon-TrendCharts'),
                    Component::custom('span')->content('访问量'),
                    Component::custom('el-tag')->content('日')->style([
                        'position' => 'absolute',
                        'right' => '0px',
                    ]),
                ])->style([
                    'position' => 'relative',
                    'max-height' => '268px',
                ])));


                $row->col([
                    "xs" => 12,
                    "sm" => 12,
                    "md" => 12,
                    "lg" => 12,
                ], Component::card($table)->header(Component::custom('div')->children([
                    Component::icon('el-icon-TrendCharts'),
                    Component::custom('span')->content('系统信息'),
                ])->style([
                    'position' => 'relative',
                    'max-height' => '268px',
                ])));

//                $timeLine = QkTimeline::make();
//                $timeLine->add('商城系统','开发中。。。。')->placement('bottom');
//                $timeLine->add('商城系统','开发中。。。。')->placement('bottom');
//                $row->col(12, Component::card($timeLine)->style([
//                    'margin-top' => '15px',
//                ]));

//                $gir = [
//                    "xs" => 3,
//                    "sm" => 3,
//                    "md" => 3,
//                    "lg" => 3,
//                ];
//                $row->col($gir, Component::iconCard('el-icon-TrendCharts', '配置', '#69c0ff'));
//                $row->col($gir, Component::iconCard('el-icon-TrendCharts', '配置', '#69c0ff'));
//                $row->col($gir, Component::iconCard('el-icon-TrendCharts', '配置', '#69c0ff'));
//                $row->col($gir, Component::iconCard('el-icon-Setting', '配置', '#69c0ff'));
//                $row->col($gir, Component::iconCard('el-icon-Setting', '配置', '#69c0ff'));
            });

        return $content;
    }

    private function getCharts()
    {
        $x = ['1/1', '1/2', '1/3', '1/4', '1/5'];
        $y1 = ['1/1' => 134, '1/2' => 134, '1/3' => 134, '1/4' => 134, '1/5' => 134];
        return [
            "columns" => ["日期", "访问用户", "下单用户", "下单率"],
            "rows" => [
                ['日期' => '1/1', '访问用户' => 1393, '下单用户' => 1093, '下单率' => 0.32],
                ['日期' => '1/2', '访问用户' => 3530, '下单用户' => 2623, '下单率' => 0.26],
                ['日期' => '1/3', '访问用户' => 2923, '下单用户' => 3230, '下单率' => 0.76],
                ['日期' => '1/4', '访问用户' => 1723, '下单用户' => 3492, '下单率' => 0.49],
                ['日期' => '1/5', '访问用户' => 4593, '下单用户' => 4293, '下单率' => 0.78],
            ],
        ];
    }

    private function getMapCharts()
    {
        $columns = ['位置', '税收', '人口', '面积'];
        $x = ['吉林', '北京', '上海', '浙江'];


        return [
            "columns" => ['位置', '税收', '人口', '面积'],
            "rows" => [
                ['位置' => '吉林', '税收' => 123, '人口' => 123, '面积' => 123],
                ['位置' => '北京', '税收' => 1223, '人口' => 2623, '面积' => 0.26],
                ['位置' => '上海', '税收' => 2923, '人口' => 3230, '面积' => 0.76],
                ['位置' => '浙江', '税收' => 1723, '人口' => 3492, '面积' => 0.49],
            ],
        ];
    }

    /**
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
        return [];
    }


}
