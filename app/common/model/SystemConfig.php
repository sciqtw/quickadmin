<?php
declare (strict_types=1);

namespace app\common\model;

use think\Model;


/**
 * Class SystemConfig
 *
 * @property bool $status 状态:1=显示,0=隐藏
 * @property int $id
 * @property int $sort 排序
 * @property string $content 变量字典数据
 * @property string $desc 配置简介
 * @property string $group 分组
 * @property string $name 配置名
 * @property string $rule 验证规则
 * @property string $title 配置标题
 * @property string $type 类型:string,text,int,bool,json,datetime,date,file
 * @property string $value 配置值
 * @package app\model
 */
class SystemConfig extends BaseModel
{
    protected $name = 'system_config';


    public static function getConfigList(string $config_type = 'admin')
    {
        $data = self::where([
            "status" =>  1,'config_type' => $config_type
        ])->order("sort desc")->select();
        $list = [];
        foreach ($data as $item) {
            $list[$item['group']][] = self::_parse($item->toArray());
        }
        return $list;
    }


    private static function _parse(array $item)
    {
        if ($item['type'] == 'json') {
            $item['value'] = empty($item['value']) ? [] : json_decode($item['value'], true);
        }
        return $item;
    }


    /**
     * @return \think\model\relation\BelongsTo
     */
    public function groupInfo()
    {
        return $this->belongsTo(SystemConfigGroup::class,'group','group');
    }

}
