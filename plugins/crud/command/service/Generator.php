<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;



class Generator
{

    public $name;

    public $output;




    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Model.stub';
    }



    protected function getItemArray($item, $field, $comment)
    {
        $itemArr = [];
        $comment = str_replace('，', ',', $comment);
        if (stripos($comment, ':') !== false && stripos($comment, ',') && stripos($comment, '=') !== false) {
            list($fieldLang, $item) = explode(':', $comment);
            $itemArr = [];
            foreach (explode(',', $item) as $k => $v) {
                $valArr = explode('=', $v);
                if (count($valArr) == 2) {
                    list($key, $value) = $valArr;
                    $itemArr[$key] = $field . ' ' . $key;
                }
            }
        } else {
            foreach ($item as $k => $v) {
                $itemArr[$v] = is_numeric($v) ? $field . ' ' . $v : $v;
            }
        }
        return $itemArr;
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
                if(strpos($value,'__(') !== false){
                    $value = $value;
                }else{
                    $value = "'{$value}'";
                }
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

    /**
     * 获取命名空间
     * @param string $app
     * @return string
     */
    protected function getNamespace(string $app): string
    {
        return 'plugins' . ($app ? '\\' . $app : '') . "\\demo\\model";
    }


    /**
     * 根据名称获取文件绝对路径
     * @param string $name
     * @return string
     */
    protected function getPathName(string $name): string
    {
        $name = str_replace('app\\', '', $name);

        return app()->getRootPath() . ltrim(str_replace('\\', '/', $name), '/') . '.php';
    }

    /**
     * 根据名称获取类名
     * @param string $name
     * @return string
     */
    protected function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }

        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($app) . '\\' . $name;
    }


    /**
     * @param $uncamelized_words
     * @param string $separator
     * @return string
     */
    protected function getCamelizeName($uncamelized_words, $separator = '_')
    {
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator);
    }

    /**
     * @param $field
     * @return string
     */
    protected function getFieldListName($field)
    {
        return $this->getCamelizeName($field) . 'List';
    }

}
