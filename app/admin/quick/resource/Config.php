<?php

namespace app\admin\quick\resource;


use app\admin\quick\actions\ConfigGroupListAction;
use app\common\service\common\BuildGroupViewService;
use quick\admin\actions\Action;
use quick\admin\components\layout\Content;
use app\common\model\SystemConfigGroup;
use quick\admin\components\layout\Row;
use quick\admin\annotation\AdminAuth;
use quick\admin\components\Component;
use quick\admin\table\Table;
use quick\admin\table\Query;
use quick\admin\form\Form;
use think\Request;


/**
 * 系统配置
 * Class SystemConfig
 * @AdminAuth(auth=true,menu=true,login=true,title="系统配置")
 * @package app\admin\quick\resource
 */
class Config extends Resource
{
    /**
     * 标题
     *
     * @var string
     */
    protected $title = '系统配置';

    /**
     * @var string
     */
    protected $description = "可以在此增改系统的变量和分组,也可以自定义分组和变量";

    /**
     * 关联模型 app\\common\\model\\DemoArticle
     *
     * @var string
     */
    protected static $model = 'app\common\model\SystemConfig';


    /**
     * 可搜索字段
     *
     * @var array
     */
    public static $search = ["id"];


    public function model(Query $model)
    {
        $model->where('config_type','admin');
        $group = SystemConfigGroup::where("id",$this->request->param('tab_key/d',0))
            ->find();
        if($group){
            $model->where('group', $group['group']);
        }
        return $model->order("sort desc,id desc");
    }


    /**
     * @return Content
     */
    protected function display()
    {

        $tree = ConfigGroupListAction::make('配置组');

        return Component::content()
            ->title($this->title())
            ->description($this->description())
            ->body(function (Row $row) use ($tree) {
                $row->props('gutter', 10);
                $row->col(["xs" => 5, "sm" => 5, "md" => 5, "lg" => 5],$tree);
                $row->col(["xs" => 19, "sm" => 19, "md" => 19, "lg" => 19], $this->getTable()->displayRender());
            });
    }

    protected function table(Table $table)
    {

        $table->column("id", "ID")->width(80)->sortable();
        $table->column("groupInfo.title", "分组")->width(100);
        $table->column("title", "配置名称")->width(200);
        $table->column("desc", "配置说明");
        $table->column("use", "使用")->display(function ($value,$row){
            return "sysConfig('{$row['group']}.{$row['name']}')";
        });
        $table->disableFilter();
        return $table;
    }


    protected function form(Form $form, Request $request)
    {

        $group = SystemConfigGroup::where("id",$this->request->get('tab_key/d',0))
            ->find();
        if($group){
            $form->extendData(['group' => $group['group']]);
        }else{
            $groupList = SystemConfigGroup::where('status',1)->select()->toArray();
            $form->select('group',"分组")->options($groupList,'group','title')->required();
        }


        $types = [
            'text' => 'text',
            'inputNumber' => 'inputNumber',
            'json' => 'json',
            'image' => 'upload',
            'textarea' => 'textarea',
            'radio' => 'radio',
            'checkbox' => 'checkbox',
        ];
        $types = BuildGroupViewService::formItem();


        $form->labelWidth(100);

        $form->select('type',"类型")->options($types)->when("in",[
            'json','radio','checkbox','select'
        ],function (Form $form){
            $form->text('content','数据列表')->textarea(6)
                ->placeholder("参数方式例如:\n1=白色\n2=红色\n3=黑色");
        })->required();
        $form->text('name','变量名')->required()->rules('alphaDash');
        $form->text('title','变量标题')->required();
        $form->text('value','变量值');
        $form->text('desc','提示信息');
        $form->text('rule','验证规则');
//        $form->checkbox('d','d')->options($types)->default(['radio']);
        return $form;
    }

    /**
     * @param \quick\admin\actions\Action $action
     * @param Request $request
     * @return \quick\admin\actions\Action
     */
    protected function addAction($action, $request)
    {
        return $action->param(['tab_key' =>$this->request->param('tab_key/d',0) ])
            ->beforeSaveUsing(function ($data, $request) {
                $data['group'] = $request->post('group');
                return $data;
            });
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return \phpDocumentor\Reflection\Types\Boolean|\quick\actions\RowAction|Action
     */
    protected function editAction(Action $action, Request $request)
    {
        return $action;
    }


    /**
     * @param Action $action
     * @param Request $request
     * @return \phpDocumentor\Reflection\Types\Boolean|Action
     */
    protected function deleteAction(Action $action, Request $request)
    {
        return $action;
    }
}
