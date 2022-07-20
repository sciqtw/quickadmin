<?php
declare (strict_types = 1);

namespace quick\admin\filter\fields;


use think\helper\Arr;

class ILike extends Like
{

    /**
     * @var string
     */
    protected $operator = 'ilike';



}