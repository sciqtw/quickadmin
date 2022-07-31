<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;



use think\facade\Db;
use think\facade\Lang;
use think\helper\Str;

class ModelsGenerator extends Generator
{

    public $name;
    public $force = false;
    public $namespace;
    public $table;
    public $fields;
    public $relations;


    /**
     * @param bool $isCreate
     * @return string|string[]
     * @throws \Exception
     */
    public function create(bool $isCreate = false)
    {



        $content =  $this->buildClass();
        $pathname = $this->getClassPath();
        if($isCreate){
            if (is_file($pathname) && !$this->force) {
                throw new \Exception($pathname.'模型已经存在');
            }

            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            file_put_contents($pathname, $content);
            return $pathname;
        }

        return $content;
    }

    /**
     * @param bool $isCreate
     * @return string|string[]
     * @throws \Exception
     */
    public function createLang(bool $isCreate = false)
    {
        $prefix = Db::newQuery()->getConfig('prefix');
        $tableName = stripos($this->table, $prefix) === 0 ? substr($this->table, strlen($prefix)) : $this->table;

        $className = str_replace('/', '\\',str_replace("model","lang",$this->namespace)."\\".Lang::getLangSet())."\\".$tableName;
        $pathname =  app()->getRootPath() . ltrim(str_replace('\\', '/', $className), '/') . '.php';

        $fields = $this->fields;
        if(empty($fields)){
            $fields = ParseModel::getTableFields($this->table);
        }

        $langList = [];
        foreach ($fields as $field) {
            if (!empty($field['lang'])) {
                $langList = array_merge($langList,$field['lang']);
            }
            $langList[$field['name']] = $field['label'];
        }




        $langListStr = [];
        foreach ($langList as $k => $v) {
            $langListStr[] = "    '" . mb_ucfirst($k) . "' => '{$v}'";
        }
        $langListStr =  implode(",\n", $langListStr);


        $stub = file_get_contents($this->getLangStub());
        $content = str_replace([
            '{%langList%}',
            ], [
            $langListStr
        ], $stub);


        if($isCreate){
            if (is_file($pathname) && !$this->force) {
                throw new \Exception($pathname.'lang已经存在');
            }

            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            file_put_contents($pathname, $content);
            return $pathname;
        }

        return $content;
    }

    /**
     * 获取类名
     * @param string $name
     * @return string
     */
    public function getModelName(string $name): string
    {
        $prefix = Db::newQuery()->getConfig('prefix');
        if(empty($this->name)){
            $tableName = stripos($name, $prefix) === 0 ? substr($name, strlen($prefix)) : $name;
            $modelName =  Str::studly($tableName);
        }else{
            $modelName = $this->name;
        }
        return $modelName;
    }

    /**
     * 获取类名
     * @param string $name
     * @return string
     */
    public function getClassName(string $name): string
    {
        $modelName = $this->getModelName($name);
        $modelName = str_replace('/', '\\', $this->namespace)."\\".$modelName;
        return $modelName;
    }


    /**
     * 获取类文件
     * @return string
     */
    public function getClassPath()
    {
        $className = $this->getClassName($this->table);
        return app()->getRootPath() . ltrim(str_replace('\\', '/', $className), '/') . '.php';
    }


    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Model.stub';
    }


    protected function getLangStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'lang.stub';
    }


    private function getMixinsStubByName($name)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs/mixins' . DIRECTORY_SEPARATOR . $name . '.stub';
    }


    public function addProperties($name,$type,$label,$len = 15)
    {
        $type = str_pad($type,8," ",STR_PAD_RIGHT);
        $name = str_pad($name,$len," ",STR_PAD_RIGHT);
        $str =  <<<EOD
* @property $type \$$name $label
EOD;
        return $str;
    }



    public function addPublics($name,$type,$label, $data = null)
    {
       $str =  <<<EOD
/** @var $type $label */
    public \${$name}
EOD;
        if (is_string($data)) {

            $str .= "= $data";
        } elseif (is_array($data)) {
            $text = $this->parseArray($data);
            $str .= "= $text";
        } elseif (is_bool($data)) {
            $text = $data ? 'true' : 'false';
            $str .= "= $text";
        }
        $str .= ";\n";
        return $str;
    }

    public function getModelClass()
    {
        $modelName = $this->getClassName($this->table);
        $namespace = $this->getModelNamespace();
        $class = str_replace($namespace . '\\', '', $modelName);
        return $class;
    }

    public function getModelNamespace()
    {
        $modelName = $this->getClassName($this->table);
        $namespace = trim(implode('\\', array_slice(explode('\\', $modelName), 0, -1)), '\\');
        return $namespace;
    }

    /**
     * @return string|string[]
     * @throws \Exception
     */
    protected function buildClass()
    {

        $fields = $this->fields;
        if(empty($fields)){
            $fields = ParseModel::getTableFields($this->table);
        }

        $namespace = $this->getModelNamespace();
        $class = $this->getModelClass();

        $rules = [];
        $attrLabels = [];
        $properties = [];
        $enumList = [];
        $langList = [];
        foreach ($fields as $field) {

            $rule = empty($field['rule']) ? []:(is_array($field['rule']) ? $field['rule']:explode('|',$field['rule']));

            if(!empty($field['require'])){
                $rule = in_array("require",$rule) ? $rule:array_merge(['require'],$rule);
            }
            if (!empty($rule)) {
                $rules[$field['name']] = implode("|", $rule);
            }

            if (!empty($field['lang'])) {
                $langList[] = array_merge($langList,$field['lang']);
            }

            if (!empty($field['label'])) {
                $attrLabels[$field['name']] = $field['label'];
            } else {
                $attrLabels[$field['name']] = $field['name'];
            }


            $properties[] = $this->addProperties($field['name'],$field['php_type'],$field['comment']);
            if(!empty($field['data'])){
                $enumList[] = $this->getEnum($field['name'],$field['data'],$field['label']);
            }

        }

        $stub = file_get_contents($this->getStub());


        $rulesStr = $this->parseRules($rules);
        $attrLabelStr = $this->parseAttrLabel($attrLabels);
        $propertieStr = implode("\n ", $properties);
        $messageStr = '';
        $constList = implode("\n\n    ",$enumList);
        $relations =  implode("\n ", $this->parseRelation());
        return str_replace([
            '{%className%}',
            '{%namespace%}',
            '{%rules%}',
            '{%attrLabels%}',
            '{%properties%}',
            '{%message%}',
            '{%constList%}',
            '{%relationMethod%}',
        ], [
            $class,
            $namespace,
            $rulesStr,
            $attrLabelStr,
            $propertieStr,
            $messageStr,
            $constList,
            $relations,
        ], $stub);
    }



    /**
     * @param $attrLabels
     * @return string|string[]
     */
    private function parseAttrLabel($attrLabels)
    {
        $stub = file_get_contents($this->getMixinsStubByName('attrLabels'));
        return str_replace(['{%attrLabels%}'], [$this->parseArray($attrLabels)], $stub);
    }


    /**
     * @param $rules
     * @return string|string[]
     */
    private function parseRules($rules)
    {
        $stub = file_get_contents($this->getMixinsStubByName('rules'));
        return str_replace(['{%rules%}'], [$this->parseArray($rules)], $stub);
    }


    /**
     * @return array
     * @throws \Exception
     */
    private function parseRelation():array
    {
        $stub = file_get_contents($this->getMixinsStubByName('relation'));
        $prefix = Db::newQuery()->getConfig('prefix');
        $relations = [];
        if(!empty($this->relations) && is_array($this->relations)){
            foreach ($this->relations as $relation){

                $relationName = stripos($relation['table'], $prefix) === 0 ? substr($relation['table'], strlen($prefix)) : $relation['table'];
                $relationMethod =  Str::camel($relationName);
                $relationClassName = str_replace("/","\\",$this->namespace."\\".Str::studly($relationName)) ;


                $relationMode = $relation['type'] === 'hasOne' ? 'hasOne':'belongsTo';
                $relationPrimaryKey = $relation['relationPrimaryKey'];
                $relationForeignKey = $relation['relationForeignKey'];

                try {
                    $relationObj = new $relationClassName;
                }catch (\Exception $e){
                    throw new \Exception('关联模型不存在:'.$relationClassName);
                }

                if(empty($relationPrimaryKey)){
                    throw new \Exception('relationPrimaryKey:'.$relationPrimaryKey);
                }

                if(empty($relationForeignKey)){
                    throw new \Exception('relationForeignKey:'.$relationForeignKey);
                }

                $relations[] = str_replace([
                    '{%relationMethod%}',
                    '{%relationClassName%}',
                    '{%relationMode%}',
                    '{%relationPrimaryKey%}',
                    '{%relationForeignKey%}',
                ], [
                    $relationMethod,
                    $relationClassName,
                    $relationMode,
                    $relationPrimaryKey,
                    $relationForeignKey,
                ], $stub);
            }
        }
        return $relations;
    }




    /**
     * @param string $field
     * @param array $itemArr
     * @param string $label
     * @return string
     */
    protected function getEnum(string $field, array $itemArr = [],string $label = '')
    {
        $field = strtoupper($field);

        foreach ($itemArr as $k => &$v) {
            $v = "__('" . mb_ucfirst($v) . "')";
        }
        unset($v);

        $itemString = $this->parseArray($itemArr);
        $fieldList = $this->getFieldListName($field);
        $methodName = 'get' . ucfirst($fieldList);
        return  <<<EOD
/**
     * {$label}
     */
    public static function {$methodName}():array
    {
        return $itemString;
    }
    
EOD;
    }



}
