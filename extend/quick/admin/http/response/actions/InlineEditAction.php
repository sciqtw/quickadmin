<?php
declare (strict_types=1);

namespace quick\admin\http\response\actions;

use quick\admin\components\Component;
use quick\admin\Element;

class InlineEditAction extends Actions
{

    /**
     * 动作
     * @var string
     */
    public $action = 'inlineEdit';


    public $inline;

    /**
     * InlineEditAction constructor.
     * @param $content
     */
    public function __construct($content)
    {


    }


    /**
     * @return mixed|\quick\admin\components\element\QuickDialog
     */
    public function getInline()
    {
        if (!$this->inline) {
            $inline = Component::inline();
            $this->inline = $inline;
        }

        return $this->inline;
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


    public function jsonSerialize()
    {
        $this->withParams(['config' => $this->getInline()]);
        return parent::jsonSerialize();
    }

}
