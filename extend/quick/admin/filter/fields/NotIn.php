<?php
declare (strict_types=1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class NotIn extends In
{

    protected $query = 'whereNotIn';


}