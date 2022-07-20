<?php
declare (strict_types=1);

namespace quick\admin\table\column;


use quick\admin\actions\Action;

class ToolsColumn extends AbstractColumn
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'index-action-field';

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @param string $type
     * @return $this|mixed
     */
    public function init($type = '')
    {

    }


    /**
     * @param array $actions
     * @return $this
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        return $this;
    }


    /**
     * @param string $value
     * @return array|void
     */
    public function display($value = '')
    {
        $this->withMeta(["value" => $this->row]);

    }


    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $this->callDisplayCallback();
        $row = $this->row;
        if(method_exists($this->row,'toArray')){
            $row = $this->row->toArray();
        }
        $actions = [];
        foreach ($this->actions as $action){
            /** @var Action $actionObject */
            $actionObject = clone $action;
            $actionObject->data($row);
            $actions[] = $actionObject ;
        }

        $this->props(['actionList' => $actions]);

        return array_merge(parent::jsonSerialize(),[]);

    }
}
