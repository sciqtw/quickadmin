<?php
declare (strict_types=1);

namespace quick\admin\components\element;


use quick\admin\components\Component;
use quick\admin\Element;

class ElTabsPane extends Element
{

    public $component = "el-tabs-pane";

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|Element
     */
    protected $label;

    /**
     * 标签内容
     * @var @var string|Element
     */
    protected $content;

    /**
     * ElTabsPane constructor.
     * @param string $name
     * @param $label
     * @param string $content
     */
    public function __construct(string $name, $label,$content = '')
    {
        $this->name($name);
        $this->label($label);
        $this->content($content);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name)
    {
        $this->name = $name;
        return $this;
    }


    /**
     * 选项卡标题
     *
     * @param string|Element $label
     * @return $this
     */
    public function label($label)
    {
        if($label instanceof Element){
            $this->children($label,'label');
        }else{
            $this->label = $label;
        }

        return $this;
    }


    /**
     * @param string|Element $content
     * @return $this|\quick\admin\metable\Metable
     */
    public function content($content)
    {
        if($content instanceof Element){
            $this->children($content,'default');
        }elseif(is_string($content) && !empty($content)){
            $this->children(Component::custom('div')->content($content),'default');
        }

        return $this;
    }

    /**
     * 禁用
     *
     * @return $this
     */
    public function disabled()
    {
        $this->props('disabled', true);
        return $this;
    }


    /**
     *  标签可以关闭
     *
     * @return $this
     */
    public function closable()
    {
        $this->props('closable', true);
        return $this;
    }


    /**
     *  标签延迟渲染
     *
     * @return $this
     */
    public function lazy()
    {
        $this->props('lazy', true);
        return $this;
    }


    public function jsonSerialize()
    {
        $this->props([
            'name' => $this->name,
            'label' => $this->label,
        ]);
        return array_merge(parent::jsonSerialize(), []);
    }

    public function getChildrenComponents(): array
    {
        return array_merge(parent::getChildrenComponents(), [$this->content]);
    }
}
