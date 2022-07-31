<?php


namespace plugins\crud\command;



class CreateModel extends Generator
{

    public $name;

    public $output;

    public function create()
    {

        $classname = $this->getClassName($this->name);

        $pathname = $this->getPathName($classname);

//        if (is_file($pathname)) {
//            $this->output->writeln('<error>' . 'eee'. ':' . $classname . ' already exists!</error>');
//            return false;
//        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname));

        /**
         * 生成ide辅助
         */
        $docGenerator = new ModelGenerator(app(), $this->output, $classname, true, true);
        $docGenerator->generate();

        $this->output->writeln('<info>' . $classname. ':' . $classname . ' created successfully.</info>');
    }




    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'Model.stub';
    }


    private function getMixinsStubByName($name)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs/mixins' . DIRECTORY_SEPARATOR . $name . '.stub';
    }

    public function addProperties($name, $data = null)
    {

        $str = "public \${$name}";
        if (is_string($data)) {

            $str .= "= $data";
        } elseif (is_array($data)) {
            $text = $this->parseArray($data);
            $str .= "= $text";
        } elseif (is_bool($data)) {
            $text = $data ? 'true' : 'false';
            $str .= "= $text";
        }
        $str .= ";";
        return $str;
    }


    /**
     * @param string $name
     * @param $model
     * @return string|string[]
     */
    protected function buildClass($name)
    {

        $fields = $this->getPropertiesFromTable();
//        dump($fields);die;

        $rules = [];
        $attrLabels = [];
        $properties = [];
        $enumList = [];
        foreach ($fields as $field) {
            if (!empty($field['rule'])) {
                $rules[$field['name']] = implode("|", $field['rule']);
            }
            if (!empty($field['label'])) {
                $attrLabels[$field['name']] = $field['label'];
            } else {
                $attrLabels[$field['name']] = $field['name'];
            }


            $properties[] = $this->addProperties($field['name']);
            if(!empty($field['data'])){
                $enumList[] = $this->getEnum($field['name'],$field['data'],$field['label']);
            }

        }

        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);


        $rulesStr = $this->parseRules($rules);
        $attrLabelStr = $this->parseAttrLabel($attrLabels);
        $propertieStr = implode("\n    ", $properties);
        $messageStr = '';
        $constList = implode("\n\n    ",$enumList);
        return str_replace([
            '{%className%}',
            '{%namespace%}',
            '{%rules%}',
            '{%attrLabels%}',
            '{%properties%}',
            '{%message%}',
            '{%constList%}'
        ], [
            $class,
            $namespace,
            $rulesStr,
            $attrLabelStr,
            $propertieStr,
            $messageStr,
            $constList,
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
     * 从数据库读取字段信息
     */
    protected function getPropertiesFromTable()
    {
        $fields = app()->db->name($this->name)->getFields();
        foreach ($fields as &$field) {
            if (!preg_match('/^([\w]+)(\(([\d]+)*(,([\d]+))*\))*(.+)*$/', $field['type'], $matches)) {
                continue;
            }
            $limit = null;
            $precision = null;
            $type = $matches[1];
            if (count($matches) > 2) {
                $limit = $matches[3] ? (int)$matches[3] : null;
            }

            if ($type === 'tinyint' && $limit === 1) {
                $type = 'boolean';
            }

            $rule = [];
            if ($field['notnull'] && empty($field['primary']) && empty($field['autoinc'])) {
                $rule[] = 'require';
            }

            if (strpos($field['comment'], ':') !== false) {
                $field['label'] = explode(":", $field['comment'])[0];
            } else {
                $field['label'] = explode(":", $field['comment'])[0];
            }

            switch ($type) {
                case 'varchar':
                case 'char':
                case 'tinytext':
                case 'mediumtext':
                case 'longtext':
                case 'text':
                case 'timestamp':
                case 'date':
                case 'time':
                case 'guid':
                case 'datetimetz':
                case 'datetime':
                case 'set':
                case 'enum':
                    $type = 'string';
                    if ($limit) {
                        $rule[] = 'max:' . $limit;
                    }
                    break;
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                    $type = 'integer';
                    $rule[] = 'integer';
                    break;
                case 'decimal':
                case 'float':
                    $type = 'float';
                    break;
                case 'boolean':
                    $type = 'boolean';
                    break;
                default:
                    $type = 'mixed';
                    break;
            }

            $field['php_type'] = $type;
            $field['rule'] = $rule;
            $data = [];
            $field['data'] = $this->getItemArray($data,$field['name'],$field['comment']);
        }
        return $fields;

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
