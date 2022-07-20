<?php
declare (strict_types=1);

namespace quick\admin\http\model\traits;


use plugins\demo\library\ParamFilterFacade;
use think\Validate as TpValidate;

trait Validate
{


    /**
     *  验证类
     *
     * @var \think\Validate
     */
    protected $validate;

    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var
     */
    private $_errors;


    /**
     * @var string
     */
    private $_scenario;


    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [];
    }


    /**
     * 验证规则
     *
     * @return array
     */
    protected function rules(): array
    {
        return [];
    }


    /**
     * @return array
     */
    protected function message(): array
    {
        return [];
    }


    /**
     * @return array
     */
    protected function checkScene(): array
    {
        return [];
    }


    /**
     * @return string
     */
    public function getScenario()
    {
        return $this->_scenario;
    }


    /**
     * @param $value
     */
    public function setScenario(string $value)
    {
        $this->_scenario = $value;
    }


    /**
     * @param string $scene
     * @param bool $batch
     * @param bool $failException
     * @param array $fields 设置验证字段范围  验证规则有且fields设置的字段才验证
     * @return Validate|TpValidate
     */
    protected function _validate($scene = '', bool $batch = false, bool $failException = true,array $fields = [])
    {
        if (empty($scene)) {
            $scene = $this->getScenario() ? $this->getScenario() : '';
        }
        $validate = $this->validate ? ($scene ? $this->validate . "." . $scene : $this->validate) : $this->getRules($scene,$fields);

        $this->filterData($scene);
        return $this->__validate($validate, $this->message(), $batch, $failException);
    }

    /**
     * 生成验证对象
     * @param string|array $validate 验证器类名或者验证规则数组
     * @param array $message 错误提示信息
     * @param bool $batch 是否批量验证
     * @param bool $failException 是否抛出异常
     * @return \quick\admin\http\model\traits\Validate
     */
    protected function __validate($validate = '', array $message = [], bool $batch = false, bool $failException = true): TpValidate
    {
        if (is_array($validate) || '' === $validate) {
            $v = new TpValidate();
            if (is_array($validate)) {
                $v->rule($validate, $this->attrLabels());
            }
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }

            $class = false !== strpos($validate, '\\') ? $validate : app()->parseClass('validate', $validate);

            $v = new $class();

            if (!empty($scene)) {
                $v->scene($scene);
            }
        }

        return $v->message($message)->batch($batch)->failException($failException);
    }


    /**
     * @param string $scene 验证场景
     * @param array $fields 只验证字段
     * @return array
     */
    public function getRules(string $scene = '',array $fields = [])
    {

        $attrsLabel = $this->attrLabels();
        [$rules, $ruleList] = [$this->_getRules($scene,$fields), []];
        foreach ($rules as $key => $rule) {

            if (!empty($attrsLabel[$key])) {

                $ruleList[$key . '|' . $attrsLabel[$key]] = $rule;

            } else {

                $ruleList[$key] = $rule;

            }
        }
        return $rules;
    }


    /**
     * @param string $scene 验证场景
     * @param array $fields 只验证字段
     * @return array
     */
    public function _getRules(string $scene = '',array $fields = []): array
    {

        [$rules, $ruleList, $scenes] = [$this->rules(), [], $this->checkScene()];
        $sceneRuleKeys = $scenes[$scene] ?? [];
        foreach ($rules as $key => $rule) {

            if (!empty($scene) && !empty($sceneRuleKeys) && !in_array($key, $sceneRuleKeys)) {
                continue;
            }

            if(!empty($fields) && !in_array($key,$fields)){
                continue;
            }

            $ruleList[$key] = $this->parseRules($key, $rule);
        }
        return $ruleList;
    }



    /**
     *
     * @param string $field 字段
     * @param string|array $rules 验证规则
     * @return array
     */
    public function parseRules(string $field, $rules)
    {
        if (is_string($rules)) {
            $rules = explode("|", $rules);
        }
        $message = $this->message();
        foreach ($rules as $key => $rule) {
            if (is_numeric($key) && is_string($rule)) {

                $ruleKey = $rule;
                $ruleValue = '';
                $item = explode(":", $rule);
                if (count($item) == 2) {
                    $ruleKey = $item[0];
                    $ruleValue = $item[1];
                    unset($rules[$key]);
                    $rules[$ruleKey] = $ruleValue;
                }

            } else {
                $ruleKey = $key;
                $ruleValue = $rule;
            }


            if (method_exists($this, $ruleKey)) {

                $msg = $field;
                if (isset($message[$field . "." . $ruleKey])) {
                    $msg = $message[$field . "." . $ruleKey];
                } elseif (isset($message[$field][$ruleKey])) {
                    $msg = $message[$field][$ruleKey];
                } elseif (isset($message[$field])) {
                    $msg = $message[$field];
                }

                $rules[$ruleKey] = function ($value, $data) use ($ruleKey, $ruleValue, $field, $msg) {
                    return $this->$ruleKey($value, $ruleValue, $data, $field, $msg);
                };
                unset($rules[$key]);

            } else {

                if (in_array($ruleKey, ['default', 'trim', 'safe', 'filter'])) {
                    $temp = [$ruleKey => $ruleValue];
                    $this->filters[$field] = isset($this->filters[$field]) ? array_merge($temp, $this->filters[$field]) : $temp;
                    unset($rules[$ruleKey]);
                }
            }
        }
        return $rules;
    }


    /**
     * @param string $scene
     */
    public function filterData($scene = '')
    {

        foreach ($this->filters as $field => $rules) {

            foreach ($rules as $key => $item) {

                if ($key == 'default' && empty($this->$field)) {

                    $this->$field = $item;

                } elseif ($key == 'trim') {

                    $this->$field = trim($this->$field);

                } elseif ($key == 'filter') {

                    if (method_exists($this, $item)) {
                        $this->$field = $this->$item($this->$field);
                    } else {
                        $this->$field = ParamFilterFacade::filter($item, $this->$field);
                    }

                }
            }
        }


    }


    /**
     * @param null $attribute
     * @return bool
     */
    public function hasErrors($attribute = null)
    {
        return $attribute === null ? !empty($this->_errors) : isset($this->_errors[$attribute]);
    }


    /**
     * @param null $attribute
     * @return array
     */
    public function getErrors($attribute = null)
    {
        if ($attribute === null) {
            return $this->_errors === null ? [] : $this->_errors;
        }

        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
    }


    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getFirstError($attribute = null)
    {
        if(empty($this->_errors)){
            return '';
        }
        if (empty($attribute)) {
            return is_array($this->_errors) ? reset($this->_errors) : $this->_errors;
        }

        return isset($this->_errors[$attribute]) ? (is_array($this->_errors) ? reset($this->_errors) : $this->_errors) : '';
    }


    /**
     * @param $attribute
     * @return mixed|null
     */
    public function getErrorMsg($attribute = null)
    {
        return $this->getFirstError($attribute);
    }

}
