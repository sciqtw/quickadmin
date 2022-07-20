<?php
declare (strict_types=1);

namespace quick\admin\table\column;


use quick\admin\contracts\ActionInterface;

class IndexColumn extends AbstractColumn implements ActionInterface
{
    /**
     * The column's component.
     *
     * @var string
     */
    public $component = 'index-show-field';

    /**
     * @var
     */
    protected $form;


    /**
     * @inheritDoc
     */
    public function load()
    {
        // TODO: Implement load() method.
    }

    /**
     * @inheritDoc
     */
    public function store()
    {
        // TODO: Implement store() method.
    }



}
