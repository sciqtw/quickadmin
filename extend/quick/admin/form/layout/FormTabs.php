<?php
declare (strict_types=1);

namespace quick\admin\form\layout;


use quick\admin\components\Component;
use quick\admin\components\QuickTabs;
use quick\admin\form\Form;

class FormTabs extends QuickTabs
{

    /**
     * @param $title
     * @param string $content
     * @param string $key
     * @return $this|QuickTabs
     */
    public function tab($title, $content = '', string $key = '')
    {
        $key = $key ?: (string)count($this->panes);

        if ($content instanceof \Closure) {
            $content = \Closure::bind($content, $this);
            $form = Form::make("form");
            call_user_func($content, $form);
            $fields = $form->getFields();
            $content = Component::custom("div")->children($fields);
        }


        $pane = Component::tabsPane($key, $title, $content);
        $this->panes = array_merge($this->panes, [$pane]);
        return $this;
    }

    /**
     * @return array
     */
    public function getChildrenComponents(): array
    {
        return array_merge(parent::getChildrenComponents(), $this->panes);
    }
}
