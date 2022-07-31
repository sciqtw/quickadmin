<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;


use think\facade\Db;
use think\helper\Str;

class BuildResource
{


    public $name;
    public $table;
    public $namespace;
    public $modelClass;
    public $modelName;
    public $fields;
    public $app;
    public $force;

    public function __construct()
    {
        $this->app = app();
    }

    /**
     * @param array $fields
     * @param bool $isCreate
     * @return string|string[]
     * @throws \Exception
     */
    public function create(array $fields,bool $isCreate = false)
    {
        $this->fields = $fields;


        $resource_name = $this->name;
        if(empty($resource_name)){

            $prefix = Db::newQuery()->getConfig('prefix');
            $tableName = stripos($this->table, $prefix) === 0 ? substr($this->table, strlen($prefix)) : $this->table;
            $resource_name =  Str::studly($tableName);
        }


        $stub = __DIR__ . "/stubs/Resource.stub";
        // 获取模板内容并替换
        $stub = file_get_contents($stub);
        $tableFields = BuildTable::buildFieldData($this->fields,$this->modelName);
        $formFields = BuildForm::buildFieldData($this->fields,$this->modelName);
        $className = $this->getClassName($this->namespace."\\".$resource_name);
        $className = str_replace("/","\\",$className);


        $namespace = trim(implode('\\', array_slice(explode('\\', $className), 0, -1)), '\\');
        $class = str_replace($namespace . '\\', '', $className);
        $pathname =  app()->getRootPath() . ltrim(str_replace('\\', '/', $className), '/') . '.php';

        $content = str_replace(
            [
                '{%formBuild%}',
                '{%tableBuild%}',
                '{%className%}',
                '{%namespace%}',
                '{%modelClass%}',
            ],
            [
                $this->buildPhpFile($formFields),
                $this->buildPhpFile($tableFields, 'table'),
                $class,
                $namespace,
                $this->modelClass
            ],
            $stub);

        if($isCreate){
            if (is_file($pathname) && !$this->force) {
                throw new \Exception($pathname.'resource已经存在');
            }
            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }

            // 写入文件
            file_put_contents($pathname, $content);
            return $pathname;
        }


        return $content;

    }



    public function buildPhpFile($data, $name = 'form')
    {

        $str = '';
        foreach ($data as $key => $field) {
            $str .= "\n        \${$name}->{$field['type']}(" . $this->parseArgs($field['args']) . ")";
            foreach ($field['methods'] as $i => $item) {
                if(!empty($item['data'])){
                    $str .= "->{$item['type']}(" . $item['data'] . ")";
                }else{
                    $str .= "->{$item['type']}(" . $this->parseArgs($item['args']) . ")";
                }
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

    /**
     * 获取文件地址
     * @param string $className 类名
     * @return string
     */
    public function getPathName(string $className): string
    {
        return $this->app->getRootPath() . ltrim(str_replace('\\', '/', $className), '/') . '.php';
    }


    /**
     * @param string $name
     * @return string
     */
    public function getClassName(string $name): string
    {
        if (strpos($name, '\\') !== false) {
            return $name;
        }

        if (strpos($name, '@')) {
            [$app, $name] = explode('@', $name);
        } else {
            $app = '';
        }
        $app = str_replace('/', '\\', $app);
        if (strpos($name, '/') !== false) {
            $name = str_replace('/', '\\', $name);
        }

        return $this->getNamespace($app) ."\\quick\\resource". '\\' . $name;
    }

    public function getNamespace(string $app): string
    {
        if(!empty($app) && strpos($app,'plugins') !== false){
            return $app;
        }
        return 'app' . ($app ? '\\' . $app : '');
    }

}
