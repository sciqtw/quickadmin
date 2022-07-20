<?php
declare (strict_types=1);

namespace quick\admin\table\traits;


use Closure;
use quick\admin\components\Component;
use quick\admin\Element;
use quick\admin\exceptions\AdminException;
use quick\admin\table\column\AbstractColumn;
use think\Exception;

trait HandleColumnTrait
{


    /**
     * @var array
     */
    protected $displayCallbacks = [];

    /**
     * @var
     */
    protected $canDisplayCallback;


    /**
     * @return array|bool
     */
    protected function resolveDisplayComponent()
    {

        $display = $this->getDisplayComponent();

        if ($display instanceof AbstractColumn) {
            $display->originalValue($this->originalValue);
            $display->value($this->value);
            $display->setRow($this->row);
            return $display->render();
        }
        return false;
    }


    /**
     * 解析column值
     *
     * @return null
     */
    public function resolveColumnValue()
    {


        if (empty($this->row)) return;


        $name = $this->name;
        $row = $this->row;
//        try {

        //关联字段
        if ($this->isRelation()) {
            $name = $this->relation . "." . $this->relationColumn;

//                if($this->row){
//                    $this->row = $row =  data_get($row, $this->relation);
//                }

        }

        if($name == '_key'){
            $name = $this->table->getKey();
        }

        if($this->table && $keyName = $this->table->getKey()){
            $pkValue = data_get($row, str_replace('->', '.', $name));
            $this->setPkValue($pkValue);
        }

        $this->value = $this->originalValue = $value = data_get($row, str_replace('->', '.', $name));

        //处理值回调
        if (!empty($this->displayCallbacks)) {
            $this->value = $this->callDisplayCallbacks($value, $row);
        }

//        }catch (\Exception $e){
//
//
//        }


    }


    /**
     *  执行displayCallback回调
     *
     * @param $value
     * @param $row
     * @return mixed
     */
    protected function callDisplayCallbacks($value, $row)
    {
        $originalValue = $value;
        try {
            foreach ($this->displayCallbacks as $callback) {
                $callback = Closure::bind($callback, $this);
                $res = call_user_func($callback, $value, $row, $originalValue);
                if($res !== null){
                    $value = $res;
                }
            }
        }catch (\Exception $e){
            halt($e->getMessage());
        }

        return $value;
    }


    /**
     * 设置显示权限回调
     *
     * @param Closure $callback
     * @return $this
     */
    public function canDisplay(Closure $callback)
    {
        $this->canDisplayCallback = $callback;
        return $this;
    }


    /**
     * 能否显示此column
     *
     * @param $reuqest
     * @return bool|mixed
     */
    public function canDisplayColumn($reuqest)
    {
        if (is_callable($this->canDisplayCallback)) {
            return call_user_func($this->canDisplayCallback, $reuqest);
        }
        return true;
    }


    /**
     * 解析字段值
     *
     * @return array
     */
    public function resolveDisplay()
    {
        $this->resolveColumnValue();

        return [
            'title' => $this->title,
            'name' => $this->name,
            'header' => Component::icon("el-icon-eleme"),
            'uriKey' => $this->uriKey(),
            'value' => $this->value,
            'originalValue' => $this->originalValue,
            'display' => $this->resolveDisplayComponent()
        ];
    }

}
