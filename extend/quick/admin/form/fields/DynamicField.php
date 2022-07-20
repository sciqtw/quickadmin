<?php
declare (strict_types=1);

namespace quick\admin\form\fields;


use quick\admin\components\Component;
use quick\admin\Element;
use quick\admin\form\Form;
use quick\admin\form\layout\Row;

class DynamicField extends Field
{


    public $component = 'form-dynamic-field';

    public $field;

    public $valueType = 'array';


    /**
     * @var Element
     */
    public $displayComponent;


//    public function transform($value)
//    {
//        $value = json_decode($value, true);
//        return $value;
//    }

    public function displayComponent()
    {

        $this->displayComponent = Component::row()->style([
            "margin-bottom" => '20px',
            "display" => 'flex',
        ])->children('');
        return $this;
    }

    public function form(\Closure $closure)
    {
        $form = Form::make();
        $form = call_user_func($closure, $form);
        if ($form instanceof Form) {
            $this->displayComponent = $form;
        }

        return $this;
    }

    public function getDisplayComponent()
    {
        if (!$this->displayComponent) {
            $this->displayComponent();
        }
        return $this->displayComponent;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        $styel = [
            "margin-left" => '10px',
            "margin-top" => '10px',
            "flex-wrap" => 'wrap',
        ];
        $form = $this->getDisplayComponent();
        $fieldJson = $this->jsonOut(Component::row()->style($styel)->attribute('type','flex')
            ->children($form->getFields())->jsonSerialize());
        $fieldData = [];
        if ($this->value) {
            $value = json_decode($this->value, true);
            foreach ($value as $item) {
                $form_item = clone $form;
                $form_item->resolve($item);
                $fieldData[] = $this->jsonOut(Component::row()->style($styel)->attribute([
                    'type' => 'flex',
                    'gutter' => 30,
                ])
                    ->children($form_item->getFields()));
            }
        }

        $this->props([
            'fieldJson' => $fieldJson,
            'fieldData' => $fieldData,
        ]);
        return array_merge(parent::jsonSerialize(), []);
    }


    protected function jsonOut($data)
    {
        return json_decode(json_encode($data));
    }
}
