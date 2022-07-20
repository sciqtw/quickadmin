<?php
declare (strict_types = 1);

namespace app\common\model;

use think\Model;


/**
 * Class SystemPlugin
 *
 * @property bool $is_deleted 删除: 1已删除 0未删除
 * @property bool $status 状态 1:启用, 0:禁用
 * @property int $create_by 创建人admin_id
 * @property int $id 自增id
 * @property int $update_by 修改人admin_id
 * @property string $avatar 图标
 * @property string $created_at 创建时间
 * @property string $deleted_at
 * @property string $desc 描述
 * @property string $display_name 显示名称
 * @property string $name 插件key
 * @property string $updated_at 更新时间
 * @property string $version 版本号
 */
class SystemPlugin extends BaseModel
{
    protected $name = 'system_plugin';


    /**
     * fdfdf
     */
    public function test()
    {

    }

}
