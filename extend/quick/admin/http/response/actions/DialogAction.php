<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

use quick\admin\components\Component;
use quick\admin\Element;

class DialogAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'dialog';

    /**
     * @var \quick\admin\components\element\QuickDialog
     */
    private $dialog;

    private $type = 'dialog';


    /**
     * DialogAction constructor.
     * @param string|Element|array $content
     * 内容 可以是一个请求对象，或者请求链接 或者一个 Element组件
     * @param string $dialog
     * @param string $type dialog|drawer
     */
    public function __construct($content ,$dialog = '',$type = 'dialog')
    {


        $this->type = $type;
        $dialogComponent = $this->getDialog();


        !empty($content) && $this->content($content);

        if (is_string($dialog)) {
            $dialog = $dialog ?? 'title';
            $dialogComponent->title($dialog);
        } elseif(is_array($dialog)){
            $dialogComponent->props($dialog);
        }elseif ($dialog instanceof \Closure) {
            call_user_func($dialog, $dialogComponent);
        }


        $this->dialog = $dialogComponent;
    }


    /**
     * @return mixed|\quick\admin\components\element\QuickDialog
     */
    public function getDialog()
    {
        if (!$this->dialog) {
            if($this->type == 'drawer'){
                $dialog = Component::drawer('title');
            }else{
                $dialog = Component::dialog('title');
            }

            $this->dialog = $dialog;
        }

        return $this->dialog;
    }



    /**
     * @param Element $component
     * @return ModalAction
     */
    public function component(Element $component)
    {
        $dialog = $this->getDialog();
        $dialog->children($component);
        return $this->withParams([__FUNCTION__ => $dialog]);
    }



    /**
     * @param string|Element|array $content
     * 内容 可以是一个请求对象，或者请求链接 或者一个 Element组件
     * @return DialogAction
     */
    public function content($content)
    {
        return $this->withParams([__FUNCTION__ => $content]);
    }

    /**
     * @param string $title
     * @return DialogAction
     */
    public function title(string $title)
    {
        $dialog = $this->getDialog();
        $dialog->title($title);
        return $this->withParams([__FUNCTION__ => $title]);
    }

    public function jsonSerialize()
    {
        $this->withParams(['config' => $this->getDialog()]);
        return parent::jsonSerialize();
    }

}
