<?php
declare (strict_types = 1);

namespace app\common\model;

use quick\admin\library\cloud\CloudService;
use quick\admin\library\tools\HttpTools;
use think\paginator\driver\Bootstrap;


/**
 * Class SystemOnlinePlugin
 * @package app\common\model
 */
class SystemOnlinePlugin
{


    public function paginate($listRows = 20, $simple = false)
    {
        $page = request()->param('page/d',1);
        $res = CloudService::instance()->pluginList($page,$listRows);
        $data = (isset($res['code']) && $res['code'] == 0) ? $res['data']['data']:[];
        $dataTotal = $res['data']['total'] ?? 0;


        $localList = SystemPlugin::where(['is_deleted' => 0])->select();
        $local = [];
        foreach ($localList as $v){
            $pluginInfo = pluginInfo($v['name']);
            if(empty($pluginInfo)){
                continue;
            }
            $v['is_install'] = 1;
            $local[$v['name']] = $v;
        }

        foreach ($data as &$item){
            if(isset($local[$item['name']])){
                $item['is_install'] = 1;
                $item['status'] = $local[$item['name']]['status'];
                unset($local[$item['name']]);
            }else{
                $item['is_install'] = 0;
            }
        }
        if(!empty($local)){
            $data = array_merge($data,array_values($local));
        }

        $paginate = Bootstrap::make($data,$listRows,$page,$dataTotal);
        return $paginate;
    }

    public function where($field, $op = null, $condition = null)
    {

        return $this;
    }
}
