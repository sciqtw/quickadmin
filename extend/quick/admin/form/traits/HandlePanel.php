<?php
declare (strict_types=1);

namespace quick\admin\form\traits;


use quick\admin\form\fields\CustomField;
use quick\admin\form\fields\JsonField;
use quick\admin\form\Form;
use quick\admin\form\layout\FormTabs;
use quick\admin\form\layout\Row;

/**
 * Trait HandlePanel
 * @package quick\form\traits
 */
trait HandlePanel
{

    public function card(string $name, $content)
    {
        $card = CustomField::make("el-card")
            ->props([
                'shadow' => 'hover'
            ])
            ->children(CustomField::make("span")->children($name)->slot("header"))
            ->style("margin", "10px 10px");
        if ($content instanceof \Closure) {
            $content = \Closure::bind($content, $this);
            $form = Form::make("form");
            call_user_func($content, $form);
            $card->children($form->getFields());
        } else {
            $card->children($content);
        }
        $this->appendField($card);
        return $card;
    }


    /**
     * @param \Closure $content
     * @return $this
     */
    public function row(\Closure $content)
    {
        if ($content instanceof \Closure) {
            $row = Row::make();
            call_user_func($content, $row);
        } else {
            $row = Row::make($content);
        }
        $this->appendField($row);
        return $this;

    }

    public function column($width, \Closure $content)
    {
        $form = static::make("form");
        call_user_func($content, $form);
        $fields = $form->getFields();
        $this->getRow()->col($width, $fields);
        return $this;
    }


    /**
     *
     */
    public function steps()
    {

    }


    /**
     * @param \Closure $content
     */
    public function tabs(\Closure $content)
    {
        $tabs = FormTabs::make();
        if ($content instanceof \Closure) {
            $content = \Closure::bind($content, $this);
            call_user_func($content, $tabs);
        }
        $this->appendField($tabs);
    }


    /**
     * @param string $column
     * @param $name
     * @param \Closure|null $closure
     * @return mixed
     */
    public function table(string $column, $name, ?\Closure $closure = null)
    {
        if($name instanceof \Closure){
            $closure = $name;
            $name = $column;
        }

        return $this->json($column, $name, $closure);
    }


    /**
     * @param string $column
     * @param string $name
     * @return JsonField
     */
    public function keyValue(string $column, string $name = '')
    {
        return $this->json($column, $name)->setKeyValue();
    }

    /**
     * @param string $column
     * @param string $name
     * @return JsonField
     */
    public function list(string $column, string $name = '')
    {
        return $this->json($column, $name)->setList();
    }



}
