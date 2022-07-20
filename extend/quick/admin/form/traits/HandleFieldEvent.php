<?php
declare (strict_types=1);

namespace quick\admin\form\traits;


use quick\admin\form\fields\CustomField;
use quick\admin\form\fields\Field;
use quick\admin\form\fields\WhenCard;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;
use quick\admin\library\tools\CodeTools;

/**
 * Trait HandlePanel
 * @package quick\form\traits
 */
trait HandleFieldEvent
{

    /**
     * 标志key,避免重复
     *
     * @var bool
     */
    public $emitKey = '';

    public $whenCondition = [];

    private $card;

    /**
     * @param $condition
     * @param $value
     * @param \Closure $closure
     * @return $this
     */
    public function whenBlur(string $condition, $value, \Closure $closure)
    {
        $this->_when("blur", $condition, $value, $closure);
        return $this;
    }


    /**
     * @param $condition
     * @param $value
     * @param \Closure $closure
     * @return $this
     */
    public function whenChange($condition, $value, \Closure $closure)
    {
        $this->_when("change", $condition, $value, $closure);
        return $this;
    }

    /**
     * @param $condition
     * @param $value
     * @param \Closure|null $closure
     * @return HandleFieldEvent
     * @throws \Exception
     */
    public function when($condition, $value, \Closure $closure = null)
    {
        if ($value instanceof \Closure) {
            $closure = $value;
        }
        if (!$closure) {
            throw new \Exception('closure 不能为空');
        }

        if (!is_string($condition) || !in_array($condition, ['=', '!=', 'has', '>', '>=', '<', '<=', 'in', 'notIn'])) {
            $value = $condition;
            $condition = '=';
        }


        return $this->whenChange($condition, $value, $closure);
    }

    /**
     * @param array $style
     * @return $this
     */
    public function cardStyle(array $style)
    {
        $this->card->style($style);
        return $this;
    }

    /**
     * @param string $type 事件类型
     * @param string $condition
     * @param $value
     * @param \Closure $closure
     * @param string $yes 匹配执行
     * @param string $no 不匹配执行
     * @return $this
     */
    protected function _when(string $type, string $condition, $value, \Closure $closure, string $yes = "show", string $no = "hidden")
    {

        $cardKey = $this->column.CodeTools::random(6, 3);
        $card = WhenCard::make();
        $card->emitKey($cardKey);
        $this->card = $card;

        $content = \Closure::bind($closure, $this);
        $form = Form::make("form");
        call_user_func($content, $form);





        // 旧方案关闭
        $fields = $form->getFilterFields();
        /** @var Field $field */
        foreach ($fields as $field) {

            if ($field->emitKey) {
                continue;
            }
            $field->emitKey = CodeTools::random(6, 3);
            $key = $field->emitKey . "_" . $field->getColumn();

            $emit = [
                'name' => $key,
                'event' => $type,
                'yes' => $yes,
                'no' => $no,
                'condition' => $condition,
                'option' => $value
            ];
            // 后端判断验证使用
            $field->whenCondition = array_merge($emit, ['form_field' => $this]);

        }


        $emits = [
            'name' => $cardKey,
            'event' => $type,
            'yes' => $yes,
            'no' => $no,
            'condition' => $condition,
            'option' => $value
        ];

        $this->emit([$emits]);
        $card->children($form->getFields());
        $this->getForm()->appendField($card);
        return $this;
    }


    /**
     * 计算当前字段是否需要输入验证
     *
     * @param $inputs
     * @return bool
     */
    public function handleWhen($inputs): bool
    {
        if (empty($this->whenCondition) || empty($this->whenCondition['form_field'])) {
            return true;
        }
        /** @var Field $whenField */
        $whenField = $this->whenCondition['form_field'];
//        $data = (Object)[];
//        $whenField->fill($inputs,$data);
        $fieldValue = $inputs[$whenField->column] ?? '';
        return $this->contrasts($this->whenCondition['condition'],$this->whenCondition['option'],$fieldValue);
    }

    private function contrasts($condition, $option, $fieldValue)
    {

        // =、>、>=、<、<=、!=、in、notIn
        switch ($condition) {
            case '=':
                if (is_array($fieldValue) && is_array($option)) {
                    if (empty(array_diff($option,$fieldValue))) {
                        return true;
                    }
                } else {
                    if ($option == $fieldValue) {
                        return true;
                    }
                }
                break;
            case ">":
                if ($fieldValue > $option) {
                    return true;
                }
                break;
            case '>=':
                if ($fieldValue >= $option) {
                    return true;
                }
                break;
            case '<':
                if ($fieldValue < $option) {
                    return true;
                }
                break;
            case '<=':
                if ($fieldValue <= $option) {
                    return true;
                }
                break;
            case '!=':

                if (is_array($fieldValue) && is_array($option)) {
                    if (!empty(array_diff($option,$fieldValue ))) {
                        return true;
                    }

                } else {
                    if ($fieldValue != $option) {
                        return true;
                    }
                }
                break;
            case "in":
                if(is_array($fieldValue)){
                    if(is_array($option) && empty(array_diff($fieldValue, $option))){
                        return true;
                    }
                }else{
                    if(is_array($option) && in_array($fieldValue,$option)){
                        return true;
                    }
                }
            case "has":
                if(is_array($fieldValue) && in_array($option,$fieldValue)){
                    return true;
                }
                break;
            case "ontIn":
                if(is_array($fieldValue) && !in_array($option,$fieldValue)){
                    return true;
                }
                break;

        }
        return false;
    }
}
