<?php
declare (strict_types = 1);

namespace plugins\crud\command\service;


use quick\admin\form\Form;

class BuildForm
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

        $field = CrudConfig::buildField($name,$title,$fieldInfo,$modelName);
        return $field;
    }


    /**
     * @param array $fields
     * @param $modelName
     * @return array
     * @throws \Exception
     */
    public static function buildFieldData(array $fields,$modelName):array
    {
        $fieldList = [];
        foreach ($fields as $field){
            if(!empty($field['relation']) || !$field['is_form'] || $field['primary']){
               continue;
            }
            $fieldList[] = static::buildFieldItem($field['name'],$field['label'],$field,$modelName);
        }

        return $fieldList;
    }

    /**
     * @param $fields
     * @param $modelName
     * @return Form|null
     * @throws \Exception
     */
    public static function buildField($fields,$modelName)
    {
        $data = self::buildFieldData($fields,$modelName);

        return Form::buildForm($data);
    }


}
