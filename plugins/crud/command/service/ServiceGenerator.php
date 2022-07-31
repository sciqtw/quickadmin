<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;



class ServiceGenerator extends Generator
{


    public $output;
    public $name;
    public $fields;
    public $force;
    public $namespace;


    /**
     * @param bool $isCreate
     * @return string|string[]
     * @throws \Exception
     */
    public function create(bool $isCreate = false)
    {

        if(empty($this->name)){
            throw new \Exception('名称不能为空');
        }
        if(empty($this->namespace)){
            throw new \Exception('命名空间不能为空');
        }


        $classname = str_replace('/', '\\', $this->namespace)."\\".$this->name;

        $pathname =app()->getRootPath() . ltrim(str_replace('\\', '/', $classname), '/') . '.php';

        $content =  $this->buildClass($classname);

        if($isCreate){
            if (is_file($pathname) && !$this->force) {
                throw new \Exception('service已经存在:'.$pathname);
            }

            if (!is_dir(dirname($pathname))) {
                mkdir(dirname($pathname), 0755, true);
            }
            file_put_contents($pathname, $content);
            return $pathname;
        }

        return $content;
    }


    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Service.stub';
    }



    private function getMixinsStubByName($name)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs/mixins' . DIRECTORY_SEPARATOR . $name . '.stub';
    }



    public function addProperties($name,$type,$label, $data = null)
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


    /**
     * @param string $name
     * @param $model
     * @return string|string[]
     */
    protected function buildClass($name)
    {

        $fields = $this->fields;
//        dump($fields);die;

        $rules = [];
        $attrLabels = [];
        $properties = [];
        foreach ($fields as $field) {
            if (!empty($field['rule'])) {
                $rules[$field['name']] = is_array($field['rule']) ? implode("|", $field['rule']):$field['rule'];
            }
            if (!empty($field['label'])) {
                $attrLabels[$field['name']] = $field['label'];
            } else {
                $attrLabels[$field['name']] = $field['name'];
            }


            $properties[] = $this->addProperties($field['name'],$field['php_type'],$field['label']);


        }

        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);


        $rulesStr = $this->parseRules($rules);
        $attrLabelStr = $this->parseAttrLabel($attrLabels);
        $propertieStr = implode("\n    ", $properties);
        $messageStr = '';
        return str_replace([
            '{%className%}',
            '{%namespace%}',
            '{%rules%}',
            '{%attrLabels%}',
            '{%properties%}',
            '{%message%}'
        ], [
            $class,
            $namespace,
            $rulesStr,
            $attrLabelStr,
            $propertieStr,
            $messageStr,
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
     * @param string $field
     * @param array $itemArr
     * @param string $label
     * @return string
     */
    protected function getEnum(string $field, array $itemArr = [],string $label = '')
    {
        echo $field;
        $field = strtoupper($field);
        $itemString = $this->parseArray($itemArr);
        return  <<<EOD
/**
     * {$label}
     */
    const {$field} = {$itemString};
EOD;
    }



}
