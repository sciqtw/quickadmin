<?php
declare (strict_types = 1);

namespace quick\admin\actions;


/**
 * Class BatchAction
 * @package quick\admin\actions
 */
abstract class BatchAction extends Action
{



    public function __construct($name = '')
    {
        $this->name = $name;
        parent::__construct();
    }

    /**
     * 动作异步数据接口
     * @return mixed
     * @throws \think\Exception
     */
    public function load()
    {
        [$pk,$pkValue] = [self::$pk,$this->request->param(self::$keyName)];
        $models = $this->getModel()->whereIn($pk,$pkValue)->select();
        return $this->resolve($models, $this->request);
    }

    /**
     * 动作提交数据接口
     * @return mixed
     * @throws \think\Exception
     */
    public function store()
    {
        [$pk,$pkValue] = [self::$pk,$this->request->param(self::$keyName)];
        $models = $this->getModel()->whereIn($pk,$pkValue)->select();
        return $this->handle($models, $this->request);
    }



}
