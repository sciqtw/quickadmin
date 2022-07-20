<?php
declare (strict_types = 1);

namespace quick\admin\table;



use quick\admin\actions\Action;
use quick\admin\components\element\ElButton;
use quick\admin\Element;

class BatchAction extends Element
{

    /**
     *  组件名称.
     *
     * @var string
     */
    public $component = "table-top-tools";

    /**
     * 动作类对象
     *
     * @var array
     */
    public $actions = [];



    public function __construct()
    {
//        $this->attribute("more-name","更多");
    }


    /**
     *  add aciton
     * @param $aciton
     * @return $this
     */
    public function add($aciton)
    {
        if($aciton instanceof  Action){
            $this->actions = array_merge($this->actions,[$aciton]);
        }elseif(is_array($aciton)){
            $this->actions = array_merge($this->actions,$aciton);
        }
        return $this;
    }


    /**
     * @param $aciton
     * @return $this
     */
    public function more($aciton)
    {
        if ($aciton instanceof Action) {
            /** @var Action */
            $aciton->withMeta(["more" => true]);
            $aciton->display(function ($display){
                if($display instanceof  ElButton){
                    $display->type("text");
                }
            });
        } elseif (is_array($aciton)) {
            foreach($aciton as &$item){

                $item->withMeta(["more" => true]);
                $item->display(function ($display){
                    if($display instanceof  ElButton){
                        $display->type("text");
                    }
                });;
            }
        }
        $this->add($aciton);
        return $this;
    }


    /**
     *  get actions
     *
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }



    /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(),[
            'component' => $this->component(),
            'actions' => $this->getActions(),
        ]);
    }

}
