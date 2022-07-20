<?php
declare (strict_types=1);

namespace quick\admin;


use quick\admin\filter\Filter;
use quick\admin\table\BatchAction;
use quick\admin\table\Query;
use quick\admin\table\ActionColumn;
use quick\admin\table\Paginate;
use quick\admin\table\Table;
use quick\admin\table\TableTools;
use think\Exception;

trait ResolvesTable
{


    /**
     * table
     *
     * @var Table
     */
    public $table;

    /**
     * table右边操作column
     * @var ActionColumn
     */
    public $actionsColumn;

    /**
     * 分页
     * @var bool|Paginate
     */
    public $paginate = false;

    protected static $model = "";

    /**
     * @return array|Query
     */
    protected function getModel()
    {
        $model = self::newModel();
        if($model){
            $query = new Query(self::newModel());
            return $this->model($query);
        }
        return [];

    }

    /**
     * @param Query $model
     * @return Query
     */
    protected function model(Query $model)
    {
        return $model;
    }


    /**
     * 获取 table
     *
     * @return Table
     * @throws Exception
     */
    protected function getTable(): Table
    {

        if (!$this->table) {
            $table = new Table($this->getModel());
            $table->showRefresh();
            $addAction = $this->getAddAction();
            if($addAction){
                $table->tools(function(TableTools $tools) use ($addAction){
                    $tools->add($addAction);
                });
            }

            $this->table = $this->table($table);




            $actions = $this->getActions();
            //配置动作
            $table->actions(function (ActionColumn $action) use ($actions) {
                $action->add($actions);
            });
            // 快捷搜索
            $table->search(static::$search);

            $filter = $this->getFilter();
            if($filter instanceof Filter){
                $table->setFilter($filter);
            }else{
                $table->disableFilter();
            }

            // 批量操作
            $batchAction = $this->getBatchActions();

            $table->batchActions(function (BatchAction $action) use ($batchAction){
                $action->add($batchAction);
            });


        }
        return $this->table;
    }


}
