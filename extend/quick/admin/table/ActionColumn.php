<?php
declare (strict_types = 1);

namespace quick\admin\table;

use Closure;
use quick\admin\Actions\Action;
use quick\admin\Quick;
use quick\admin\table\column\AbstractColumn;
use quick\admin\table\column\ToolsColumn;
use quick\admin\table\traits\HandleColumnTrait;
use think\helper\Str;

class ActionColumn extends Column
{
    use HandleColumnTrait;


    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'el-column';


    /**
     * title
     * @var
     */
    public $title;

    /**
     * column
     * @var
     */
    public $name = '_actions';

    /**
     * actions
     * @var
     */
    public $actions = [];


    /**
     * 禁用删除
     *
     * @var bool
     */
    protected $disableDelete = true;


    /**
     * 直接显示动作个数，超出动作按钮折叠起来
     *
     * @var int
     */
    protected $showNum = 0;

    /**
     * Column constructor.
     * @param $name
     * @param string $title
     */
    public function __construct($title = '')
    {
        $this->title = $title;
        $this->attribute('fixed', 'right');
        $this->attribute('width', '200');
        $this->attribute('align', 'center');
        $this->initialize();
    }


    /**
     * Initialize.
     */
    protected function initialize()
    {

    }


    /**
     *  获取操作的uri密钥。
     * @return string
     */
    public function uriKey()
    {
        return Str::snake(Quick::humanize($this));
    }


    /**
     *  add aciton
     * @param $aciton
     * @return $this
     */
    public function add($aciton)
    {
        if ($aciton instanceof Action) {
            $this->actions = array_merge($this->actions, [$aciton]);
        } elseif (is_array($aciton)) {
            $this->actions = array_merge($this->actions, $aciton);
        }
        return $this;
    }


    /**
     *
     */
    public function showNum(int $num)
    {
        $this->showNum = $num;
        return $this;
    }

    /**
     * @param $aciton
     * @return $this
     */
    public function more($aciton){
        if ($aciton instanceof Action) {
            /** @var Action */
            $aciton->withMeta(["more" => true]);
        } elseif (is_array($aciton)) {
            foreach($aciton as &$item){
                $item->withMeta(["more" => true]);
            }
        }
        $this->add($aciton);
        return $this;
    }


    /**
     * @param $request
     * @param $model
     * @return array
     */
    public function getActionsFilter($request, $model)
    {
        return $actions = array_merge(collect($this->getActions())->filter(function (Action $action) use ($request, $model) {
            return $action->handleCanRun($request, $model);
        })->toArray());
    }


    /**
     *  get actions
     * @return array
     */
    public function getActions()
    {
        return $this->setActionShow($this->actions);
    }


    /**
     * 获取显示组件
     *
     * @return mixed
     */
    public function getDisplayComponent()
    {
        if(!$this->displayComponent){
            $this->displayUsing(ToolsColumn::class);
        }
        return $this->displayComponent;
    }

    /**
     * 获取当前类的子类
     *
     * @return array
     */
    public function getChildrenComponents(): array
    {
        return array_merge(
            (array)$this->getChildren(),
            $this->actions
        );
    }


    /**
     * 解析displayComponent
     *
     * @return array|bool
     */
    protected function resolveDisplayComponent()
    {
        $displayComponent = $this->getDisplayComponent();

        if ($displayComponent instanceof AbstractColumn) {
            if($this->row){
                $actions = $this->getActionsFilter(request(),$this->row);
            }else{
                $actions = $this->getActions();
            }


            $displayComponent->originalValue($this->originalValue);
            $displayComponent->value($this->value);
            $displayComponent->setRow($this->row);
            $displayComponent->setTable($this->table);
            $displayComponent->setActions($actions);
            return $displayComponent->render();
        }
        return false;
    }


    /**
     * 配置前端按钮展示折叠状态
     *
     * @param $actions
     * @return mixed
     */
    private function setActionShow($actions)
    {
        if($this->showNum && count($actions) > $this->showNum ){
            list($num,$showNum)  = [1,$this->showNum];
            foreach($actions as &$action){

                if( $showNum <= $num){
                    $action->withMeta(["more" => true]);
                }else{
                    $action->withMeta(["more" => false]);
                }
                $num++;
            }
        }
        return $actions;

    }


}
