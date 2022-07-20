<?php
declare (strict_types=1);

namespace quick\admin\table\action;


use quick\admin\component\ElPopover;
use quick\admin\Element;
use quick\admin\form\fields\CustomField;
use quick\admin\form\Form;
use quick\admin\table\column\AbstractColumn;
use think\Exception;

class TextColumn extends ActionColumn
{
    /**
     * The column's component.
     *
     * @var string
     */
    public $component = 'index-text-field';

    public $form;



    /**
     * @param $method
     * @param string $rule
     * @return $this
     */
    public function init($rule = '')
    {
        $this->form = Form::make('')
//            ->hideFooter()
            ->inline()
            ->style("padding", "0px")
            ->labelWidth(0);

        $this->canRun(function () {
            return true;
        });


        if (func_num_args() == 2) {
            list($method, $rule) = func_get_args();
            $this->{$method}($rule);
        }


        return $this;
    }

    /**
     * @param string $rule
     */
    public function text($rule = '')
    {
        $text = $this->getForm()->text($this->name, '');
        if($rule instanceof \Closure){
            call_user_func($rule,$text);
        }elseif($rule){
            $text->rules($rule);
        }
    }

    /**
     * @param string $rule
     */
    public function number($rule = '')
    {
        $text = $this->getForm()->text($this->name, '')->number();
        if ($rule) {
            $text->rules($rule);
        }
    }

    /**
     * @param Element $data
     */
    public function slotPopover(Element $data)
    {
        $this->popover('')->top()->click();
        $this->children($data, "popover");
    }


    /**
     * @return Form
     */
    protected function getForm()
    {
        return $this->form;
    }

    /**
     * @return mixed|string
     */
    public function loadUrl()
    {
        return $this->_actionUrl("show");
    }

    /**
     * @return mixed|string
     */
    public function storeUrl()
    {
        return $this->_actionUrl("update");
    }

    private function _actionUrl(string $func)
    {
        $module = str_replace('.', '/', app('http')->getName());
        $resource = app()->request->route('resource');
        $uriKey = $this->uriKey();
        return "{$module}/resource/{$resource}/{$uriKey}/{$func}";
    }

    public function store()
    {
        return $this->handle();
    }


    /**
     * @param string $value
     * @return array|void
     */
    public function display($value = '')
    {
        if ($this->isCanRun()) {
            $form = $this->getForm();
            $form->resolve([$this->name => $this->value]);
            $this->slotPopover($form);
        }
        return $this->jsonSerialize();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {

        return array_merge(parent::jsonSerialize(), [
            'submitUrl' => $this->storeUrl(),
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
