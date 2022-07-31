<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;


use app\common\service\common\BuildGroupViewService;

class ParseModel
{


    /**
     * @param $table
     * @return array
     */
    public static function getTableFields($table)
    {
        $fields = app()->db->table($table)->getFields();
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



            $rule = [];
            $field['require'] = false;
            if ($field['notnull'] && empty($field['primary']) && empty($field['autoinc'])) {

                if(!in_array($field['name'],['deleted_at','created_at','updated_at','is_deleted'])){
                    $field['require'] = true;
                }

            }

            if (strpos($field['comment'], ':') !== false) {
                $field['label'] = explode(":", $field['comment'])[0];
            } else {
                $field['label'] = $field['comment'];
            }

            $field['limit'] = $limit;
            $field['is_form'] = true;
            $field['is_table'] = true;
            $php_type = $type;
            switch ($type) {
                case 'varchar':
                case 'char':
                case 'tinytext':
                case 'mediumtext':
                case 'longtext':
                case 'text':
                    if ($limit) {
                        $rule[] = 'max:' . $limit;
                    }
                case 'timestamp':
                case 'date':
                case 'time':
                case 'guid':
                case 'datetimetz':
                case 'datetime':
                case 'set':
                case 'enum':
                    $php_type = 'string';
                    break;
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'int':
                case 'bigint':
                    $php_type = 'integer';
                    $rule[] = 'integer';
                    break;
                case 'decimal':
                case 'float':
                    $php_type = 'float';
                    $rule[] = 'float';
                    break;
                case 'boolean':
                    $php_type = 'boolean';
                    $rule[] = 'integer';
                    break;
                default:
                    $php_type = 'mixed';
                    break;
            }
            if ($type === 'tinyint' && $limit === 1) {
                $type = 'boolean';
            }

            [$data,$langList] = self::getItemArray([], $field['name'], $field['comment']);
            $field['php_type'] = $php_type;
            $field['type'] = $type;
            $field['rule'] = $rule;
            $field['data'] = $data;
            $field['lang'] = $langList;
            $field['show'] = 'text';
            $field['form'] = 'text';
            self::handleFieldType($field);
//            BuildGroupViewService::formItem();
            if($field['primary']){
                $field['is_form'] = false;
            }
            if(in_array($field['name'],CrudConfig::formIgnore())){
                $field['is_form'] = false;
            }
            if(in_array($field['name'],CrudConfig::tableIgnore())){
                $field['is_table'] = false;
            }
        }
        return $fields;

    }

    protected static function handleFieldType(&$field)
    {
        $field['form'] = "text";
        $field['show'] = "text";

        $fieldConfig = CrudConfig::resolveField($field['name'],$field['type'],$field['comment']);
        if(!empty($fieldConfig)){
//            $field['form'] = "text1";
            $field['form'] = $fieldConfig['key'];
            $field['show'] = $fieldConfig['key'];
        }


//        $config = BuildGroupViewService::formItem();


    }


    protected static function getItemArray($item, $field, $comment)
    {
        $itemArr = [];
        $langArr = [];
        $comment = str_replace('，', ',', $comment);
        if (stripos($comment, ':') !== false && stripos($comment, ',') && stripos($comment, '=') !== false) {
            list($fieldLang, $item) = explode(':', $comment);
            $langArr[$field] = $fieldLang;
            foreach (explode(',', $item) as $k => $v) {
                $valArr = explode('=', $v);
                if (count($valArr) == 2) {
                    list($key, $value) = $valArr;
                    $itemArr[$key] = $field . ' ' . $key;
                    $langArr[$itemArr[$key]] = $value;
                }
            }
        } else {
            $langArr[$field] = $field;
            foreach ($item as $k => $v) {
                $itemArr[$v] = is_numeric($v) ? $field . ' ' . $v : $v;
            }
        }
        return [
            $itemArr,
            $langArr
        ];
    }


}
