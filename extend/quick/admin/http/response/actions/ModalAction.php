<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

use Closure;
use quick\admin\components\Component;
use quick\admin\Element;


/**
 * Class ModalAction
 * @package quick\admin\http\response\actions
 */
class ModalAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'modal';


    /**
     * @var \quick\admin\components\element\QuickDialog
     */
    private $dialog;


    /**
     * ModalAction constructor.
     * @param Element $component
     * @param string|Closure|array $title
     */
    public function __construct(Element $component, $title = 'title')
    {
        $dialog = $this->getDialog();

        if ($title instanceof \Closure) {
            call_user_func($title, $dialog);
        }elseif(is_array($title)){
            $dialog->attribute($title);
        } elseif(is_string($title)){
            $dialog->title($title);
        }
        $dialog->children($component);
        $this->dialog = $dialog;
    }


    /**
     * @return \quick\admin\components\element\QuickDialog
     */
    public function getDialog()
    {
        if (!$this->dialog) {
            $dialog = Component::dialog('title');
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
     * @param string $title
     * @return DialogAction
     */
    public function title(string $title)
    {
        return $this->withParams([__FUNCTION__ => $title]);
    }

    public function jsonSerialize()
    {
        $this->withParams(['component' => $this->getDialog()]);
        return parent::jsonSerialize();
    }

}
