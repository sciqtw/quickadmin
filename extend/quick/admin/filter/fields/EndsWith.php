<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class EndsWith extends Like
{

    /**
     * @var string
     */
    protected $exprFormat = '%{value}';

}