<?php
declare (strict_types=1);

namespace quick\admin\http\model;

use quick\admin\http\model\traits\Validate;
use think\Model as BaseModel;

/**
 * Class BaseModel
 * @package app\model
 */
class Model extends BaseModel
{
    use Validate;


    /**
     * @var
     */
    private $_errors;

    /**
     * @var bool
     */
    private $safeOnly = true;

    /**
     * @var string
     */
    private $_scenario;

    /**
     * @var bool
     */
    public $failException = true;

    /**
     * 批量验证
     *
     * @var bool
     */
    public $batchValidate = false;


    /**
     * @param mixed ...$arguments
     * @return static
     */
    public static function make(...$arguments)
    {
        return new static(...$arguments);
    }


    /**
     * 新增前事件
     *
     * @param Model $model
     * @return mixed
     */
    public static function onBeforeInsert($model)
    {
        $scene = $model->getScenario() ?? 'add';
        return $model->autoValidate($model, $scene);
    }


    /**
     * 更新前事件
     * @param Model $model
     * @return array|string|true
     */
    public static function onBeforeUpdate($model)
    {
        $scene = $model->getScenario() ?? 'edit';
        return $model->autoValidate($model, $scene);
    }


    /**
     * 更新数据
     * @access public
     * @param array $data 数据数组
     * @param mixed $where 更新条件
     * @param array $allowField 允许字段
     * @param string $suffix 数据表后缀
     * @return \think\Model
     */
    public static function update(array $data, $where = [], array $allowField = [], string $suffix = '')
    {

        $model = new static();
        $model->setScenario('_update');
        if (!empty($allowField)) {
            $model->allowField($allowField);
        }

        if (!empty($where)) {
            $model->setUpdateWhere($where);
        }

        if (!empty($suffix)) {
            $model->setSuffix($suffix);
        }

        $model->exists(true)->save($data);

        return $model;
    }


    /**
     * @param Model $model
     * @param string $scene
     * @return array|string|true
     */
    private function autoValidate(Model $model, $scene = '')
    {
        $scene = $scene ? $scene : $this->getScenario();
        $fields = $scene === '_update' ?  array_keys($model->getData()):[];
        $v = $this->_validate($scene, $this->batchValidate, $this->failException,$fields);
        $res = $v->check($model->getData());
        if (true !== $res) {
            $err = $v->getError();
            $this->_errors = is_array($err) ? $err : [$err];
            return false;
        }
        return true;
    }


    /**
     * 验证
     * @param string $scene
     * @return bool
     */
    public function validate($scene = '')
    {
        $scene = $scene ? $scene : $this->getScenario();
        $v = $this->_validate($scene, $this->batchValidate, $this->failException);
        $res = $v->check($this->getData());
        if (true !== $res) {
            $err = $v->getError();
            $this->_errors = is_array($err) ? $err : [$err];
            return false;
        }
        return true;
    }


    /**
     * @param string $scene
     * @return $this
     */
    public function setScenario(string $scene)
    {
        $this->_scenario = $scene;
        return $this;
    }


    /**
     * @return string
     */
    protected function getScenario()
    {
        return $this->_scenario;
    }


    /**
     * 设置数据对象值
     * @access public
     * @param array $data 数据
     * @param bool $set 是否调用修改器
     * @param array $allow 允许的字段名
     * @return BaseModel
     */
    public function data(array $data, bool $set = false, array $allow = [])
    {

        if ($this->safeOnly) {
            $rules = array_keys($this->rules());
            $scenario = $this->getScenario();
            $scenarios = $this->checkScene();
            if (!empty($scenario) && !empty($scenarios)) {
                $allow = !isset($scenarios[$scenario]) ? [] : $scenarios[$scenario];
            } else {
                $allow = $rules;
            }
            $allow = array_intersect($rules, $allow);

            if (empty($allow)) $data = [];// 安全模式，每个字段都必须配置验证rule才允许批量赋值
        }

        return parent::data($data, $set, $allow);
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
     * @return mixed
     */
    public function getError()
    {
        return $this->getFirstError();
    }


//    /**
//     * @param null $attribute
//     * @return array
//     */
//    public function getErrors($attribute = null)
//    {
//        if ($attribute === null) {
//            return $this->_errors === null ? [] : $this->_errors;
//        }
//
//        return isset($this->_errors[$attribute]) ? $this->_errors[$attribute] : [];
//    }
//
//
//    /**
//     * @param $attribute
//     * @return mixed|null
//     */
//    public function getFirstError($attribute = null)
//    {
//        if (empty($attribute)) {
//            return reset($this->_errors);
//        }
//
//        return isset($this->_errors[$attribute]) ? reset($this->_errors[$attribute]) : null;
//    }


}
