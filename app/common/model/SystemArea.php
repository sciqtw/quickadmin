<?php
declare (strict_types=1);

namespace app\common\model;

use quick\admin\http\model\Model;
use quick\admin\library\tools\ArrTools;
use quick\admin\library\tools\TreeArray;

/**
 * Class SystemArea
 *
 * @property integer $id              ID
 * @property integer $pid             父id
 * @property string $shortname       简称
 * @property string $name            名称
 * @property string $mergename       全称
 * @property integer $level           层级:0=省,1=市,2=区县
 * @property string $pinyin          拼音
 * @property string $code            长途区号
 * @property string $zip             邮编
 * @property string $first           首字母
 * @property string $lng             经度
 * @property string $lat             纬度
 *
 * @package app\common\model
 */
class SystemArea extends Model
{


    /**
     * 验证规则
     * @return array
     */
    protected function rules(): array
    {
        return [
            'id' => 'integer',
            'pid' => 'integer',
            'shortname' => 'max:100',
            'name' => 'max:100',
            'mergename' => 'max:255',
            'level' => 'integer',
            'pinyin' => 'max:100',
            'code' => 'max:100',
            'zip' => 'max:100',
            'first' => 'max:50',
            'lng' => 'max:100',
            'lat' => 'max:100',
        ];
    }


    /**
     * @return array
     */
    protected function attrLabels(): array
    {
        return [
            'id' => 'ID',
            'pid' => '父id',
            'shortname' => '简称',
            'name' => '名称',
            'mergename' => '全称',
            'level' => '层级',
            'pinyin' => '拼音',
            'code' => '长途区号',
            'zip' => '邮编',
            'first' => '首字母',
            'lng' => '经度',
            'lat' => '纬度',
        ];
    }


    /**
     * 层级
     */
    public static function getLevelList(): array
    {
        return [
            0 => __('Level 0'),
            1 => __('Level 1'),
            2 => __('Level 2'),
        ];
    }


    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function cascaderOptions()
    {
        $data = cache("_cascaderOptions");
        if($data){
            return $data;
        }
        $list = self::field('pid,name as label,id as value')->select()->toArray();
        $list = TreeArray::arr2tree($list,'value','pid','children');
        cache("_cascaderOptions",$list);
        return $list;
    }


    /**
     * 根据ids获取地址数据
     * @param array $ids
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getValuesByIds(array $ids)
    {
        $list = self::where("id","in",$ids)->select()->toArray();
        return $list;
    }
}
