<?php
declare (strict_types=1);

namespace app\common\service\common;

use app\common\model\SystemGroup;
use app\common\service\CommonService;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;
use quick\admin\table\Table;

/**
 * Class CommonAdminUserService
 * @package app\common\service\common
 */
class BuildGroupViewService extends CommonService
{


    /**
     * @return array
     */
    public static function config()
    {
        $config = [
            'text' => [
                'name' => '文本框',
                'form' => [
                    'type' => 'text',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'number' => [
                'name' => '数字文本框',
                'form' => [
                    'type' => 'text',
                    'methods' => [
                        ['type' => 'number', 'args' => [0,9999999,1]]
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [

                    ],
                ],
            ],
            'inputNumber' => [
                'name' => '数字输入框',
                'form' => [
                    'type' => 'inputNumber',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'textarea' => [
                'name' => '多行本框',
                'form' => [
                    'type' => 'text',
                    'methods' => [
                        ['type' => 'textarea', 'args' => [4]]
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'quill' => [
                'name' => '富文本',
                'form' => [
                    'type' => 'quill',
                    'methods' => [
                        ['type' => 'textarea', 'args' => [4]]
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [
                        ['type' => 'html']
                    ],
                ],
            ],
            'switch' => [
                'name' => '开关',
                'form' => [
                    'type' => 'switch',
                    'methods' => [
                        ['type' => 'activeValue', 'args' => ['1']],
                        ['type' => 'inactiveValue', 'args' => ['0']],
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'radio' => [
                'name' => '单选',
                'form' => [
                    'type' => 'radio',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],

            'checkbox' => [
                'name' => '多选',
                'form' => [
                    'type' => 'checkbox',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'select' => [
                'name' => '下拉选择',
                'form' => [
                    'type' => 'select',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'upload_image' => [
                'name' => '单图上传',
                "form" => [
                    'type' => 'upload',
                    'methods' => [
                        [
                            'type' => 'image',
                            'args' => []
                        ]
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [
                        [
                            'type' => 'image',
                            'args' => [40]
                        ]
                    ],
                ],
            ],
            'upload_images' => [
                'name' => '多图上传',
                'form' => [
                    'type' => 'upload',
                    'methods' => [
                        [
                            'type' => 'images',
                            'args' => []
                        ]
                    ],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'keyValue' => [
                'name' => 'json键值',
                'form' => [
                    'type' => 'keyValue',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
        ];
        return $config;
    }


    public static function formItem()
    {
        $data = [];
        foreach (self::config() as $k => $item) {
            $data[$k] = $item['name'];
        }
        return $data;
    }


    /**
     * 分组表单
     * @return Form
     */
    public static function editGroupForm()
    {
        $form = Form::make("设置密码")->labelWidth(150);
        $form->props('label-position', 'top');

        $form->labelWidth(100);
        $form->row(function (Row $row) {
            $row->props('gutter', 5);
            $row->col(8, function ($form) {
                $form->text('title', "数据组名称")->rules('require');


            });
            $row->col(8, function ($form) {
                $form->text('info', "数据简介")->rules('require');
            });
            $row->col(8, function ($form) {
                $form->text('name', "数据字段")->rules('require')
                    ->creationRules('unique:SystemGroup');
            });
        });


        $form->dynamic('fields', '自定义字段')->form(function (Form $form) {


            $style = [
                'flex' => '1',
                'width' => '100%',
                'margin-right' => '10px',
            ];
            $options = self::formItem();
            $rules = [
                'require' => 'require',
                'integer' => 'integer',
                'float' => 'float',
                'boolean' => 'boolean',
                'email' => 'email',
            ];
            $form->text('title', '字段名称')->style($style)->placeholder('字段title')->required();
            $form->text('name', '字段name')->style($style)->placeholder('字段name')->rules('alphaDash')->required();

            $form->text('rule', '规则')->style($style)->placeholder('字段rule');
            $form->select('type', '字段类型')->options($options)->placeholder('字段类型')
                ->required()
                ->when('in', ['select', 'radio', 'checkbox','json'], function (Form $form) {
                    $form->text('param', '字段配置')->textarea()
                        ->style([
                            'width' => '100%',
                        ])
                        ->hiddenLabel()
                        ->placeholder("参数方式例如:\n1=白色\n2=红色\n3=黑色");
                });
            return $form;

        })->fillUsing(function ($data) {
            return json_encode($data['fields']);
        });
        return $form;
    }


    /**
     * 分组数据表单
     * @param array $list
     * @param null $form
     * @return mixed|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getGroupDataForm(array $list, $form = null)
    {


        $fields = [];
        foreach ($list as $item) {

            $fieldConfig = $this->getConfigByName($item['type'],"form");
            $fieldConfig['args'] = [
                $item['name'], $item['title']
            ];


            $methods = $this->getMethods($item);

            if ($item['rule']) {
                $methods[] = ['type' => 'rules', 'args' => [$item['rule']]];
            }
            $methods[] = ['type' => 'default', 'args' => [$item['value']]];

            if (!empty($item['desc'])) {
                $methods[] = ['type' => 'help', 'args' => [$item['desc']]];
            }


            $fieldConfig['methods'] = array_merge($methods, $fieldConfig['methods']);
            $fields[] = $fieldConfig;
        }
        $form = Form::buildForm($fields,$form);

        return $form;

    }

    private function getMethods($field)
    {
        $methods = [];

        if (in_array($field['type'], ['radio', 'select', 'checkbox'])) {
            $options = explode("\n", $field['content']);
            $optionList = [];
            foreach ($options as $item) {
                $tmp = explode("=", $item);
                if (count($tmp) == 2) {
                    $optionList[$tmp[0]] = $tmp[1];
                }

            }
            $methods[] = ['type' => 'options', 'args' => [
                empty($optionList) ? [] : $optionList
            ]];
        }

        return $methods;
    }


    public function getGroupDataTable($groupName, $table)
    {
        $groupInfo = SystemGroup::where('name', $groupName)->json(['fields'])->find();
        if ($groupInfo) {
            $groupInfo = $groupInfo->toArray();
        }
        $table->column("id", "ID")->width(80)->sortable();


        if ($groupInfo && $groupInfo['fields']) {
            $tableColumns = [];
            foreach ($groupInfo['fields'] as $field) {
                $column = $this->getConfigByName($field['type'],"table");
                $column['args'] = [
                    'value->' . $field['name'], $field['title']
                ];

                if (in_array($field['type'], ['radio', 'select'])) {
                    $options = explode("\n", $field['param']);
                    $optionList = [];
                    foreach ($options as $item) {
                        $tmp = explode("=", $item);
                        if (count($tmp) == 2) {
                            $optionList[$tmp[0]] = $tmp[1];
                        }

                    }
                    $column['methods'][] = ['type' => 'using', 'args' => [
                        empty($optionList) ? [] : $optionList
                    ]];
//                    halt($column);
                }


                $tableColumns[] = $column;
            }

            $table = Table::buildTable($tableColumns, $table);
        }

        $table->column("sort", "排序");
        $table->column("status", "启用状态")->switch(function () {
            $this->inactiveText("禁用")
                ->activeText("启用")->width(55);
        })->width(90);
        return $table;
    }


    private function getConfigByName($name,$type = 'form')
    {
        $configList = self::config();
        $config = empty($configList[$name]) ? $configList['text']:$configList[$name];
        return $config[$type];
    }
}
