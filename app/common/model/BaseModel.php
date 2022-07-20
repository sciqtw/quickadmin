<?php
declare (strict_types=1);

namespace app\common\model;

use quick\admin\http\model\Model;

/**
 * Class BaseModel
 * @package app\model
 */
class BaseModel extends Model
{

    protected $error;

    /**
     * @var bool
     */
    public $failException = false;


    protected $autoWriteTimestamp = 'datetime';
    // 定义时间戳字段名
    protected $createTime = 'created_at';
    protected $updateTime = 'updated_at';



}
