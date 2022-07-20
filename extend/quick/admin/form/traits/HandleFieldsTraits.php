<?php
declare (strict_types=1);

namespace quick\admin\form\traits;


use app\Request;
use quick\admin\form\fields\Field;
use quick\admin\Form\Fields;
use quick\admin\QuickRequest;
use think\facade\Validate;


/**
 * Trait handleFieldsTraits
 * @package quick\form\traits
 */
trait HandleFieldsTraits
{
    /**
     * form fields.
     *
     * @var array
     */
    public $fields = [];


    /**
     * @var callable
     */
    protected $beforeResolveCallback;


    /**
     * @param $inputs
     * @param null $model
     * @return array
     */
    public function fill($inputs, $model = null): array
    {

        !$model && $model = (Object)[];
        collect($this->getFilterFields())->map(function (Fields\Field $field) use ($inputs, $model) {
            return $field->fill($inputs, $model);

        });
        return empty($model) ? [] : (array)$model;
    }


    /**
     * 赋值构建表单
     * @param $model
     * @return $this
     */
    public function resolve($model)
    {
        if ($this->beforeResolveCallback instanceof \Closure) {
            $beforeResolveCallback = \Closure::bind($this->beforeResolveCallback,$this);
            $model = call_user_func($beforeResolveCallback, $model);
        }


        $this->_resolveFields($this->getFilterFields(), $model);
        return $this;
    }


    /**
     * 数据处理前回调
     *
     * @param callable $beforeResolveCallback
     * @return $this
     */
    public function beforeResolveUsing(callable $beforeResolveCallback)
    {
        $this->beforeResolveCallback = $beforeResolveCallback;

        return $this;
    }


    /**
     * @param $fields
     * @param $model
     * @return \think\Collection
     */
    private function _resolveFields($fields, $model)
    {

        return collect($fields)->each(function (Fields\Field $field) use ($model) {
            if ($field instanceof Field) {
                return $field->resolve($model);
            }
            return $field;
        });

    }

    /**
     * 表单提交前的回调
     *
     * @param callable $fillCallback
     * @return $this
     */
    public function fillUsing(callable $fillCallback)
    {
        $this->fillCallback = $fillCallback;

        return $this;
    }


    /**
     * 获取提交数据
     *
     * @param \think\Request $request
     * @param int $validate
     * @return array
     * @throws \quick\admin\Exception
     */
    public function getSubmitData(\think\Request $request, int $validate = 0)
    {
        $inputs = $request->param();
        $data = $this->fill($inputs);
        $type = 'all';
        if ($validate) {
            if($validate == 1){
                $type = 'creation';
            }elseif($validate == 2){
                $type = 'update';
            }
            $rules = $this->getRules($type,$data);
            $vali = Validate::rule($rules);
            if (!$vali->batch(true)->check($data)) {
                quick_abort(422, "表单验证有误", ["errors" => $vali->getError(),'data' => $data,'$rules' => $rules]);
            }
        }
        return $data;
    }

}
