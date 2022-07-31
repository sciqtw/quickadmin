<?php
declare (strict_types=1);

namespace plugins\crud\command\service;


use think\helper\Str;

class CrudConfig
{

    public static function config()
    {
        $config = [
            'text' => [
                'name' => '文本框',
                'formSuffix' => [],
                'formType' => ['varchar','char','tinytext'],
                'form' => [
                    'type' => 'text',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],
            'textarea' => [
                'name' => '多行本框',
                'formSuffix' => [],
                'formType' => ['mediumtext','longtext','text'],
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
                'formSuffix' => [],
                'formType' => ['longtext','text'],
                'form' => [
                    'type' => 'quill',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [
                        ['type' => 'html','args' => []]
                    ],
                ],
            ],
            'number' => [
                'name' => '数字文本框',
                'formSuffix' => [],
                'formType' => ['decimal','float'],
                'form' => [
                    'type' => 'text',
                    'methods' => [
                        ['type' => 'number', 'args' => [0, 9999999, 0.01]]
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
                'formSuffix' => [],
                'formType' => ['integer','int'],
                'form' => [
                    'type' => 'inputNumber',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [],
                ],
            ],

            'date' => [
                'name' => '日期',
                'formSuffix' => ['time'],
                'formType' => ['bigint','datetime','int'],
                'form' => [
                    'type' => 'date',
                    'methods' => [],
                ],
                'table' => [
                    'type' => 'column',
                    'methods' => [
                        ['type' => 'date', 'args' => []],
                    ],
                ],
            ],
            'switch' => [
                'name' => '开关',
                'formSuffix' => ['switch'],
                'formType' => ['tinyint','boolean'],
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
                'formSuffix' => ['data','type'],
                'formType' => ['enum','tinyint','boolean'],
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
                'formSuffix' => ['data'],
                'formType' => ['set'],
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
                'formSuffix' => ['list'],
                'formType' => ['set'],
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
                'formSuffix' => ['image'],
                'formType' => ['varchar'],
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
                'formSuffix' => ['images'],
                'formType' => ['varchar'],
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
                'formSuffix' => ['json'],
                'formType' => ['varchar'],
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


    /**
     * @param string $name
     * @param string $type
     * @param string $comment
     * @return mixed|string
     */
    public static function resolveField(string $name, string $type, string $comment = '')
    {

        $fieldList = self::config();
        $field = '';
        foreach ($fieldList as $key => $item) {
            if(empty($item['formSuffix']) && empty($item['formType'])){
                continue;
            }
            $sort = 0;// 优先级
            if (!empty($item['formSuffix'])) {
                $isSuffix = Str::endsWith($name, $item['formSuffix']);
                $sort++;
            }else{
                $isSuffix = true;
            }

            if(!empty($item['formType'])){
                $isType = in_array($type,$item['formType']);
                $sort++;
            }else{
                $isType = true;
            }


            if($isSuffix && $isType){
                $item['sort'] = $sort;
                $item['key'] = $key;
                if(empty($field) || $sort >= $field['sort']){
                    $field = $item;
                }
            }

        }


        return $field;

    }

    public static function formItem()
    {
        $data = [];
        foreach (self::config() as $k => $item) {
            $data[$k] = $item['name'];
        }
        return $data;
    }

    public static function formIgnore()
    {
        return ['deleted_at', 'created_at', 'updated_at', 'is_deleted'];
    }

    public static function tableIgnore()
    {
        return ['deleted_at', 'updated_at', 'is_deleted'];
    }


    public static function buildTableField(string $name,string $title,array $fieldInfo,string $modelName)
    {
        $fieldType = $fieldInfo['form'];

        $fieldList =  self::config();
        if(empty($fieldList[$fieldType])){
            throw new \Exception('字段类型不存在');
        }

        $fieldConfig  = $fieldList[$fieldType];
        $methods = $fieldConfig['table']['methods'];


        if(in_array($fieldType,['radio','checkbox','select']) && !empty($fieldInfo['data'])){
            $options = [];
            foreach ($fieldInfo['data'] as $key => $item){
                $options[$key] = $fieldInfo['lang'][$item] ?? $item;
            }
            $data = "{$modelName}::get".Str::studly($name)."List()";
            $methods[] = [
                'type' => 'replace',
                'data' => $data,
                'args' => [
                    $options
                ]
            ];
        }


        $title = empty($title) ? ucfirst($name):$title;
        return [
            'type' => $fieldConfig['table']['type'],
            'name' => $name,
            'args' => [
                $name,
                $title,
            ],
            'methods' => $methods
        ];
    }


    public static function buildField(string $name,string $title,array $fieldInfo,string $modelName)
    {
        $fieldType = $fieldInfo['form'];

        $fieldList =  self::config();
        if(empty($fieldList[$fieldType])){
            throw new \Exception('字段类型不存在');
        }

        $fieldConfig  = $fieldList[$fieldType];
        $methods = $fieldConfig['form']['methods'];


        if(in_array($fieldType,['radio','checkbox','select']) && !empty($fieldInfo['data'])){
            $options = [];
            foreach ($fieldInfo['data'] as $key => $item){
                $options[$key] = $fieldInfo['lang'][$item] ?? $item;
            }
            $data = "{$modelName}::get".Str::studly($name)."List()";
            $methods[] = [
                'type' => 'options',
                'data' => $data,
                'args' => [
                    $options
                ]
            ];
        }

        $rules = !empty($fieldInfo['rule']) ? explode("|",$fieldInfo['rule']):[];
        if(!empty($fieldInfo['require'])){
            $rules = in_array("require",$rules) ? $rules:array_merge(['require'],$rules);
        }

        if(!empty($rules)){
            if(in_array($fieldType,['radio','checkbox','select','number','switch','inputNumber'])){
                $rules = array_diff($rules,['float','integer']);
            }
            if(!empty($rules)){
                $methods[] = [
                    'type' => 'rules',
                    'args' => [
                        implode("|",$rules)
                    ]
                ];
            }

        }



        $title = empty($title) ? ucfirst($name):$title;
        return [
            'type' => $fieldConfig['form']['type'],
            'name' => $name,
            'args' => [
                $name,
                $title,
            ],
            'methods' => $methods
        ];
    }
}
