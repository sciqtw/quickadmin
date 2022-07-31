<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;

use quick\admin\table\Table;

class BuildTable
{



    /**
     * @param $name
     * @param $title
     * @param $fieldInfo
     * @param $modelName
     * @return array
     * @throws \Exception
     */
    public static function buildFieldItem($name,$title,$fieldInfo,$modelName):array
    {
        $field = CrudConfig::buildTableField($name,$title,$fieldInfo,$modelName);
        return $field;
//        $field = self::getField($fieldInfo['form']);
//        $methods = $field['table']['methods'];
////        if(!empty($fieldInfo['rule'])){
////            $methods[] = [
////                'type' => 'rules',
////                'args' => [
////                    $fieldInfo['rule']
////                ]
////            ];
////        }
//
//        $title = empty($title) ? ucfirst($name):$title;
//        return [
//            'type' => $field['table']['type'],
//            'args' => [
//                $name,
//                $title,
//            ],
//            'methods' => $methods
//        ];
    }


    /**
     * @param $fields
     * @param $modelName
     * @return array
     * @throws \Exception
     */
    public static function buildFieldData($fields,$modelName)
    {
        $fieldList = [];
        foreach ($fields as $field){
            if(!$field['is_table']){
                continue;
            }
            $name = $field['name'];
            if(!empty($field['relation'])){
                $name = $field['relation'].".".$field['name'];
            }
            $fieldList[] = static::buildFieldItem($name,$field['label'],$field,$modelName);
        }
        return $fieldList;
    }


    /**
     * @param $fields
     * @param $modelName
     * @return Table|null
     * @throws \Exception
     */
    public static function buildField($fields,$modelName)
    {


        $data = self::buildFieldData($fields,$modelName);

        return Table::buildTable($data);
    }

    /**
     * @param $fields
     * @return mixed
     * @throws \Exception
     */
    public static function buildResource($fields)
    {
        $fieldList = [];
        foreach ($fields as $field){
            $fieldList[] = static::buildFieldItem($field['name'],$field['label'],$field);
        }


        return Table::buildTable($fieldList);
    }

}
