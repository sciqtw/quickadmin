<?php
declare (strict_types = 1);

namespace plugins\crud\command;


use quick\admin\form\Form;

class ResourceGenerator
{

    public $name;


    public $output;



    public function create()
    {

        $pathname = __DIR__ . "/Test.stub";
        // 写入文件
        file_put_contents($pathname, $this->buildClass());
    }

    public function buildClass()
    {
        $stub = __DIR__ . "/stubs/Resource.stub";
        // 获取模板内容并替换
        $stub = file_get_contents($stub);
        $data = [1 => '232', 3 => 'eee'];
        $list = [
            [
                'type' => 'text',
                'args' => [
                    'name',
                    '名称',
                ],
                'methods' => [
                    [
                        'type' => 'required',
                        'args' => []
                    ],
                ]

            ],
            [
                'type' => 'select',
                'args' => [
                    'set',
                    '选择',
                ],
                'methods' => [
                    [
                        'type' => 'options',
                        'args' => [
                            [
                                's' => '男',
                                'w' => '女',
                                2 => true,
                                'st' => [
                                    'ss' => 3,
                                    's' => 'e',
                                    'sd' => false
                                ]
                            ],
                            'id',
                            'name'
                        ]
                    ],
                    [
                        'type' => 'filterable',
                        'args' => [
                            false,
                            true,
                            2,
                            '3'
                        ]
                    ]
                ]

            ]
        ];

        return str_replace(
            ['{%formBuild%}', '{%tableBuild%}'],
            [
                $this->buildPhpFile($list),
                $this->buildPhpFile($list, 'table'),
            ],
            $stub);
    }


    public function select($data)
    {

//        $text = print_r($data, true);
//        $text = "\n            " . implode(",\n            ", $data) . ",\n        ";
        $text = $this->parseArray($data);
        $str = <<<CODE
\$form->select('df2d')
CODE;

        $str .= "->options($text)";
        $str .= ";";
        return $str;
    }

    public function getForm()
    {
        return Form::make();
    }

    public function buildPhpFile($data, $name = 'form')
    {

        $str = '';
        foreach ($data as $key => $field) {
            $str .= "\n        \${$name}->{$field['type']}(" . $this->parseArgs($field['args']) . ")";
            foreach ($field['methods'] as $i => $item) {
                $str .= "->{$item['type']}(" . $this->parseArgs($item['args']) . ")";
            }
            $str .= ";";
        }

        return $str;

    }

    public function parseArgs(array $args)
    {
        $str = "";
        foreach ($args as $k => $v) {

            if (is_array($v)) {
                $str .= $this->parseArray($v);
            } elseif (is_string($v)) {
                $str .= "'{$v}'";
            } elseif (is_bool($v)) {
                $str .= $v ? 'true' : 'false';
            } else {
                $str .= $v;
            }
            if (($k + 1) < count($args)) {
                $str .= ", ";
            }
        }
        return $str;

    }


    /**
     * 解析数组参数
     *
     * @param $array
     * @param string $span
     * @return string
     */
    public function parseArray($array, $span = "        ")
    {
        $xSpan = $span . "    ";
        $str = "[";
        foreach ($array as $key => $value) {

            if (is_array($value)) {
                $value = $this->parseArray($value, $span . "    ");
            } elseif (is_string($value)) {
                $value = "'{$value}'";
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            if (is_string($key)) {
                $key = "'{$key}'";
            }

            $str .= "\n{$xSpan}{$key} => " . $value . ",";
        }
        $str .= "\n{$span}]";
        return $str;
    }

    public function buildForm($list)
    {
        return Form::BuildForm($list);

    }


}
