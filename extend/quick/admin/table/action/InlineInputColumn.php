<?php
declare (strict_types=1);

namespace quick\admin\table\action;


use quick\admin\components\Component;
use quick\admin\form\Form;
use quick\admin\http\response\JsonResponse;

class InlineInputColumn extends ActionColumn
{
    /**
     * The column's component.
     *
     * @var string
     */
    public $component = 'index-show-field';

    public $form;


    /**
     * @param $method
     * @param string $rule
     * @return $this
     */
    public function init($rule = '')
    {
        $this->form = Form::make('')
            ->hideFooter()
            ->inline()
            ->size("default")
            ->style([
                "padding" =>  "0px",
                "display" =>  "flex",
            ])
            ->setClass('inline-form')
            ->attribute('inline-message',true)
            ->labelWidth(0);

        $this->canRun(function () {
            return true;
        });


        if (func_num_args() == 2) {
            list($method, $rule) = func_get_args();
            $this->{$method}($rule);
        }
        $size = '20';

        $this->form->footer('', '')
            ->props('width','50px')
            ->hideReset()
            ->showCancel()
            ->submitBtn(Component::icon("el-icon-CircleCheck")->style([
                'margin-right' => '10px'
            ])->color("#67c23a")->size($size))
            ->cancelBtn(Component::icon("el-icon-CircleClose")->color("red")->size($size));

        //设置图标
        $this->suffix(Component::icon('el-icon-edit')
            ->color("#409EFF")->style('cursor', 'pointer'));

        return $this;
    }

    /**
     * @param string $rule
     */
    public function text($rule = '')
    {
        $text = $this->getForm()->text($this->name, '')
            ->style([
                'width','200px',
            ])
            ->props('inline-message',true)->hiddenLabel();
        if ($rule instanceof \Closure) {
            call_user_func($rule, $text);
        } elseif ($rule) {
            $text->rules($rule);
        }
        $this->displayUsing(function ($value, $row, $originalValue) {
            $this->inlineContainer();
        });
    }

    /**
     * @param string $rule
     */
    public function text1($rule = '')
    {
        $text = $this->getForm()->text($this->name, '');
        if ($rule instanceof \Closure) {
            call_user_func($rule, $text);
        } elseif ($rule) {
            $text->rules($rule);
        }
        $this->displayUsing(function ($value, $row, $originalValue) {
            $this->popoverContainer();
//            $this->modalCOntainer();
        });
    }

    /**
     * @param string $rule
     */
    public function number($rule = '')
    {
        $text = $this->getForm()->text($this->name, '')->number()->hiddenLabel();
        if ($rule) {
            $text->rules($rule);
        }
        $this->displayUsing(function ($value, $row, $originalValue) {
            $this->inlineContainer();
        });
    }


    /**
     * @param array $options
     * @param string $rule
     */
    public function select(array $options,$rule = 'require')
    {
        $rule = 'require';
        $text = $this->getForm()->select($this->name, '')->options($options,  "id", "name")->hiddenLabel();
        if ($rule) {
            $text->rules($rule);
        }
        $this->displayUsing(function ($value, $row, $originalValue) {
            $this->inlineContainer();
        });
    }





    /**
     * @return Form
     */
    protected function getForm()
    {
        return $this->form;
    }



    public function store()
    {
        return $this->handle();
    }


    public function inlineContainer()
    {
        $form = clone $this->getForm();
        $form->url($this->storeUrl());
        $form->extendData([
            static::$keyValue => $this->getPkValue(),
            static::$colValue => $this->value,
        ]);
        $form->resolve([$this->name => $this->value]);
        $this->children($form, "editing");
        $this->inlineEdit($form);
    }


    public function modalContainer()
    {
        $form = clone $this->getForm();
        $form->resolve([$this->name => $this->value]);
        $this->children($form, "editing");
        $this->modal($this->name, $form);
    }


    public function popoverContainer()
    {
        $form = clone $this->getForm();
        $form->resolve([$this->name => $this->value]);
        $this->children($form, "editing");
        $this->popover($form);
    }


    /**
     * @return \think\response\Json
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function handle()
    {
        $value = request()->param($this->name);
        $pkValue = request()->param(static::$keyValue);
        $model = $this->resource::newModel()->where($this->table->getKey(),$pkValue)->find();
        if (!$model) {
            quick_abort(500, '资源不存在'.$this->name);
        }

        if($this->column->relation){
            $relation = $model->{$this->column->relation};
            $relation->{$this->column->relationColumn} = $value;
            if($relation){
                $model = $relation;
            }
        }else{
            $model->{$this->name} = $value;
        }
        $res = $model->save();
        if ($res !== false) {
            return JsonResponse::make()->success("修改成功")->send();
        } else {
            return JsonResponse::make()->error("修改失败:".$model->getErrorMsg())->send();
        }


    }


    /**
     * @param string $value
     * @return array|void
     */
    public function display($value = '')
    {

    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        return array_merge(parent::jsonSerialize(), [
            'submitUrl' => $this->storeUrl(),
            'row' => $this->row,
            'fieldsUrl' => $this->loadUrl()
        ]);

    }

    /**
     * @inheritDoc
     */
    public function load()
    {
        // TODO: Implement show() method.
    }
}
