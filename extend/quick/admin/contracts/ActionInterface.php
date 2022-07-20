<?php
declare (strict_types=1);

namespace quick\admin\contracts;



interface ActionInterface
{


    /**
     * @return mixed
     */
    public function load();

    /**
     * @return mixed
     */
    public function store();


}
