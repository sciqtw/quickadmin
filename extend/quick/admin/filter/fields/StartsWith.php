<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class StartsWith extends Like
{

    /**
     * @var string
     */
    protected $exprFormat = '{value}%';

}