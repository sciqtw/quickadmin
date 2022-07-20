<?php


namespace quick\annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Routes
 * @Annotation
 * @package plugins\demo\library\annotation
 */
final class ApiReturn extends Annotation
{
    /**
     *  参数
     *
     * @var string 你
     */
    public $type = "Object";

    public $sample;

    public function test()
    {
        echo 'test';
    }
}