<?php
declare (strict_types = 1);

namespace quick\admin\form\traits;




use quick\admin\form\fields\Field;

/**
 * Trait handleFieldsTraits
 * @package quick\form\traits
 */
trait HandleValidateTraits
{


    /**
     * 获取全场景验证规则
     *
     * @param string $type  all|update|creation
     * @param array $inputs 表单值
     * @return mixed
     */
    public function getRules($type = 'all',$inputs = [])
    {
        [$rules,$fields ]= [[],$this->getFilterFields()];
        collect($fields)->each(function(Field $field) use (&$rules,$type,$inputs){
            $fieldRules = $field->getRules($type,$inputs);
            !empty($fieldRules) && $rules[$field->getColumn()."|".$field->getTitle()] = $fieldRules;
        });
        return $rules;
    }



}
