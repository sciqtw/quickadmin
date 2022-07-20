<?php
declare (strict_types=1);

namespace quick\admin;


use phpDocumentor\Reflection\Types\Boolean;
use quick\admin\actions\Action;
use quick\admin\actions\AddAction;
use quick\admin\actions\EditAction;
use quick\admin\form\Form;
use think\Request;

trait ResolvesActions
{

    /**
     * @var
     */
    public $form;

    /**
     * 编辑动作类
     * @var string
     */
    public $editAction = "quick\\admin\\actions\\EditAction";

    /**
     * 添加动作类
     * @var string
     */
    public $addAction = "quick\\admin\\actions\\AddAction";

    /**
     * 删除动作类
     * @var string
     */
    public $deleteAction = "quick\\admin\\actions\\DeleteAction";


    /**
     * 资源动作类
     * @var array
     */
    public static $actions = [];


    /**
     * 定义删除动作
     * @param Action $action
     * @param Request $request
     * @return Action|Boolean
     */
    protected function deleteAction(Action $action, Request $request)
    {
        return false;
    }

    /**
     * 定义编辑动作
     * @param \quick\actions\RowAction $action
     * @param Request $request
     * @return \quick\actions\RowAction|Boolean
     */
    protected function editAction(Action $action, Request $request)
    {
        return false;
    }


    /**
     * 定义添加动作
     * @param Action $action
     * @param Request $request
     * @return Action|Boolean
     */
    protected function addAction(Action $action, Request $request)
    {
        return false;
    }

    /**
     * @return mixed
     */
    protected function getForm()
    {
        if (!$this->form) {
            $this->form = $this->form(Form::make($this->title()), $this->request);
        }
        return $this->form;
    }


    /**
     * @return bool|mixed|Action
     */
    protected function getDeleteAction()
    {
        if ($this->deleteAction) {

            $deleteAction = invoke($this->deleteAction);
            $deleteAction->setResource($this);
            $deleteAction = $this->deleteAction($deleteAction, $this->request);
            if ($deleteAction instanceof Action) {
                return $deleteAction;
            }
        }
        return false;
    }


    /**
     * @return bool|EditAction|\quick\actions\RowAction
     */
    protected function getEditAction()
    {
        if ($this->editAction) {

            /** @var EditAction $editAction */
            $editAction = invoke($this->editAction);
            $editAction = $editAction->setResource($this);
            $editAction->setForm($this->getForm());
            $editAction = $this->editAction($editAction, $this->request);
            if ($editAction instanceof Action) {
                return $editAction;
            }

        }
        return false;
    }

    /**
     * @return bool|mixed|Action
     */
    protected function getAddAction()
    {
        if ($this->addAction) {
            /** @var AddAction $editAction */
            $addAction = invoke($this->addAction);
            $addAction->setResource($this);
            $addAction->setForm($this->getForm());
            $addAction = $this->addAction($addAction, $this->request);
            if ($addAction instanceof Action) {
                return $addAction;
            }
        }
        return false;
    }


    /**
     * @return array
     */
    protected function getActions(): array
    {

        $actions = [];
        $editAction = $this->getEditAction();
        if ($editAction) {
            $actions[] = $editAction;
        }
        $deleteAction = $this->getDeleteAction();
        if ($deleteAction) {
            $actions[] = $deleteAction;
        }
        $actions = array_merge($actions, $this->actions());

        return $actions;
    }


    /**
     * 获取资源定义的批量操作
     *
     * @return array
     */
    protected function getBatchActions(): array
    {
        return $this->batchActions();
    }


}
