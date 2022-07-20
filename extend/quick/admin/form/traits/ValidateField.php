<?php
declare (strict_types = 1);

namespace quick\admin\form\traits;


use quick\admin\library\FormValidate;

/**
 * Trait ValidateFieldTraits
 * @package quick\form\traits
 */
trait ValidateField
{

    /**
     * 验证规则
     *
     * @var array
     */
    private $rules = [];

    /**
     * 创建场景验证规则
     *
     * @var array
     */
    private $creationRules = [];

    /**
     * 更新场景验证规则
     *
     * @var array
     */
    private $updateRules = [];


    /**
     * 获取验证规则
     * @param string $type update|creation
     * @param array $inputs
     * @return array
     */
    public function getRules(string $type = '',$inputs = [])
    {
        if(!empty($inputs) && !$this->handleWhen($inputs)){
            return [];
        }

        if($type == 'update'){
            return array_merge($this->_getRules(), $this->updateRules);
        }else if($type == 'creation'){
            return array_merge($this->_getRules(), $this->creationRules);
        }
        return $this->_getRules();
    }


    /**
     * @return array
     */
    private function _getRules()
    {

        return $this->rules;
    }


    /**
     * 添加创建验证规则
     *
     * @param $rules
     * @return ValidateField
     */
    public function creationRules($rules)
    {
        return $this->_setRules(__FUNCTION__,$rules);
    }

    /**
     * 添加更新数据验证规则
     *
     * @param $rules
     * @return ValidateField
     */
    public function updateRules($rules)
    {
        return $this->_setRules(__FUNCTION__,$rules);
    }


    /**
     * 转换前端验证规则
     * @param array $rules
     * @return array
     */
    public function transformRulesToVue(array $rules)
    {
        $vali = FormValidate::instance();
        $vueRules = [];
        foreach($rules as $rule) {
            $vueRules[] = $vali->transform($rule,$this->getTitle(),$this->getValueType());
        }
        return $vueRules;
    }

    /**
     * 添加验证规则
     *
     * @param string $rules
     * @return ValidateField
     */
    public function rules(string $rules)
    {
        return $this->_setRules(__FUNCTION__,$rules);
    }

    /**
     * @param $name
     * @param $rules
     * @return $this
     */
    private function _setRules($name,$rules)
    {
        if(!empty($rules) && is_string($rules)){
            $rules = explode("|",$rules);
        }elseif(is_callable($rules)){
            $rules = [$rules];
        }
        $data = $this->{$name} ?? [];
        $this->{$name} = array_merge($data,$rules);
        return $this;
    }


    /**
     * @return $this
     */
    public function required()
    {
        $this->rules("require");
        return $this;
    }
}
