<?php
declare (strict_types = 1);

namespace quick\admin\table\traits;


use quick\admin\form\layout\Col;
use quick\admin\table\ActionColumn;
use quick\admin\table\BatchAction;
use quick\admin\table\Column;
use quick\admin\table\TableTools;

trait HandleActionColumnTrait
{

    /**
     * 操作列
     *
     * @var ActionColumn
     */
    protected $actionsColumn;

    /**
     * _key Column
     *
     * @var Column
     */
    protected $keyColumn;


    /**
     * 批量操作
     *
     * @var BatchAction
     */
    protected $batchActions;


    /**
     *
     * @var TableTools
     */
    protected $tableTools;



    /**
     * 设置批量操作
     *
     * @param BatchAction $batchActions
     * @return $this
     */
    private function setBatchActions(BatchAction $batchActions)
    {
        $this->batchActions = $batchActions;
        return $this;
    }


    /**
     * 设置操作行
     */
    private function setActionsColumn($column)
    {
        $this->actionsColumn = $column;
        return $this;
    }



    /**
     * 设置操作行
     */
    private function setKeyColumn($column)
    {
        $this->keyColumn = $column;
        return $this;
    }


    /**
     * 设置批量操作
     *
     * @param BatchAction $batchActions
     * @return $this
     */
    private function setTableTools(TableTools $tableTools)
    {
        $this->tableTools = $tableTools;
        return $this;
    }



    /**
     * @return BatchAction
     */
    public function getBatchActions(): BatchAction
    {
        if (!($this->batchActions instanceof BatchAction)) {
            $this->setBatchActions(BatchAction::make('批量操作'));
        }
        return $this->batchActions;
    }


    public function getKeyColumn()
    {
        if (!($this->keyColumn instanceof Column)) {

            $this->setKeyColumn(Column::make('_key')->setTable($this));
        }

        return $this->keyColumn;
    }


    /**
     * @return ActionColumn
     */
    public function getActionColumn(): ActionColumn
    {
        if (!($this->actionsColumn instanceof ActionColumn)) {

            $this->setActionsColumn(ActionColumn::make(__('操作'))->setTable($this));
        }

        return $this->actionsColumn;
    }


    /**
     * @return TableTools
     */
    public function getTableTools(): TableTools
    {
        if (!($this->tableTools instanceof TableTools)) {

            $this->setTableTools(TableTools::make(''));
        }

        return $this->tableTools;
    }


    /**
     * @param \Closure $tools
     * @return $this
     */
    public function tools(\Closure $tools)
    {
        call_user_func($tools,$this->getTableTools());
        return $this;
    }


    /**
     * @param \Closure $action
     * @return ActionColumn
     */
    public function actions(\Closure $action)
    {
        call_user_func($action,$this->getActionColumn());
        return $this->getActionColumn();
    }


    /**
     * 添加批量动作
     *
     * @param \Closure $action
     * @return $this
     */
    public function batchActions(\Closure $action)
    {
        call_user_func($action,$this->getBatchActions());
        return $this;
    }
}
