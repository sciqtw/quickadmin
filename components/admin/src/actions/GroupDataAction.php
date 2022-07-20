<?php
declare(strict_types=1);

namespace components\admin\src\actions;


use app\common\model\SystemAdminInfo;
use app\common\model\SystemGroup;
use quick\admin\actions\RowAction;
use quick\admin\annotation\AdminAuth;
use quick\admin\table\Table;

/**
 * 组合数据
 * @AdminAuth(auth=true,menu=true,login=true,title="组合数据")
 * @package app\admin\resource\example\actions
 */
class GroupDataAction extends RowAction
{

    /**
     * 关联模型
     *
     * @var string
     */
    protected static $model = "app\common\model\SystemGroup";


    protected function initAction()
    {

        $this->getDisplay()->type('text');
        $this->push("/admin/resource/group_data/index");
    }


    /**
     * @param Table $table
     * @return Table
     * @throws \Exception
     */
    protected function table(Table $table)
    {

        $groupInfo = SystemGroup::where('id', 30)->json(['fields'])->find();
        if ($groupInfo) {
            $groupInfo = $groupInfo->toArray();
        }

        $table->attribute('border', false);
        $table->column("id", "ID")->width(80)->sortable();
        if ($groupInfo && $groupInfo['fields']) {
            foreach ($groupInfo['fields'] as $field) {
                $column = $table->column('value->' . $field['name'], $field['title']);
                if ($field['type'] == 'image') {
                    $column->image();
                }
            }
        }

        $table->column("status", "状态")->display(function ($value) {

            return $value;
        });
        $table->column("sort", "排序");
//        $table->disableActions();


        return $table;
    }


    /**
     * @return mixed|\quick\admin\http\response\JsonResponse
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function store()
    {

        $form = $this->form();
        $data = $form->getSubmitData($this->request, 3);
        $userId = app()->auth->getAdminId();;
        if (empty($userId)) {
            return $this->response()->error('设置失败');
        }
        $userModel = SystemAdminInfo::find($userId);
        $userModel->nickname = $data['nickname'] ?? '';
        $userModel->phone = $data['phone'] ?? '';
        $userModel->email = $data['email'] ?? '';
        if ($userModel->save() !== false) {
            return $this->response()->success('设置成功', $data);
        }
        return $this->response()->error('设置失败');
    }


}
