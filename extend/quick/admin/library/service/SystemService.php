<?php
declare (strict_types=1);

namespace quick\admin\library\service;

use app\common\model\SystemGroup;
use app\common\model\SystemGroupData;
use quick\admin\Service;

/**
 * Class SystemService
 * @package quick\admin\library\service
 */
class SystemService extends Service
{

    /**
     * 配置数据缓存
     * @var array
     */
    protected $data = [];

    /**
     * 绑定配置数据表
     * @var string
     */
    protected $table = 'SystemConfig';


    /**
     * @param string $name 配置key
     * @param string $value 配置值
     * @param string $plugin 插件标识
     * @param string $type 配置值类型
     * @param array $extend 配置其他参数
     * @return int|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function set(string $name, $value = '', $plugin = 'admin',string $type = 'string', array $extend = [])
    {
        [$group, $field] = $this->_parse($name, 'base');
        if (is_array($value) && !in_array($type, ['json', 'image', 'images', 'checkbox'])) {
            $count = 0;
            foreach ($value as $k => $v) {
                $count += $this->set("{$field}.{$k}", $v,$plugin);
            }
            return $count;
        } else {

            if (is_array($value)) {
                if ($type == 'json') {
                    $value = json_encode($value);
                } elseif (in_array($type, ['image', 'checkbox'])) {
                    $value = implode(',', $value);
                }

            }

            $this->app->cache->delete($this->table);
            $map = ['group' => $group, 'name' => $field,'plugin' => $plugin];
            $data = array_merge($map, ['value' => $value, 'type' => $type], $extend);
            $query = $this->app->db->name($this->table)->master(true)->where($map);
            return (clone $query)->count() > 0 ? $query->update($data) : $query->insert($data);
        }
    }

    /**
     * 读取配置数据
     * @param string $name
     * @param  $default
     * @param string $plugin 插件
     * @return array|mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get(string $name = '',  $default = '',string $plugin = 'admin')
    {
        if (empty($this->data)) {
            $this->app->db->name($this->table)->select()->where('plugin',$plugin)->map(function ($item) {
                if ($item['type'] == 'json') {
                    $item['value'] = json_decode($item['value'], true);
                }
                $this->data[$item['group']][$item['name']] = $item['value'];
            });
        }
        [$group, $field, $outer] = $this->_parse($name, 'base');
        if (empty($name)) {
            return $this->data;
        } elseif (isset($this->data[$group])) {
            $group = $this->data[$group];
            if ($outer !== 'raw') {
                foreach ($group as $k => $vo) {
                    $group[$k] = is_array($vo) ? $vo : htmlspecialchars($vo);
                }
            }
            return $field ? ($group[$field] ?? $default) : $group;
        }
        return $default;
    }

    public function groupList(string $group,string $plugin)
    {
        return $this->app->db->name($this->table)->master(true)
            ->where([
                'group' => $group,
                'plugin' => $plugin,
            ])
            ->column('value','name');
    }



    /**
     * 解析缓存名称
     * @param string $rule 配置名称
     * @param string $group 配置默认分组
     * @return array
     */
    private function _parse(string $rule, string $group = 'base'): array
    {
        if (stripos($rule, '.') !== false) {
            [$group, $rule] = explode('.', $rule, 2);
        }
        [$field, $outer] = explode('|', "{$rule}|");
        return [$group, $field, strtolower($outer)];
    }

    /**
     * 判断运行环境
     * @param string $type 运行模式（dev|demo|local）
     * @return boolean
     */
    public function checkRunMode(string $type = 'dev'): bool
    {
        $domain = $this->app->request->host(true);
        $isDemo = is_numeric(stripos($domain, 'quickadmin.cn'));
        $isLocal = in_array($domain, ['127.0.0.1', 'localhost']);
        if ($type === 'dev') return $isLocal || $isDemo;
        if ($type === 'demo') return $isDemo;
        if ($type === 'local') return $isLocal;
        return true;
    }


    /**
     * 获取分组数据
     *
     * @param string $key
     * @param string $plugin
     * @param array $default
     * @return array|mixed
     */
    public function getGroupData(string $key, string $plugin = 'admin', array $default = [])
    {


        $isCache = !$this->app->isDebug();
        $cacheKey = "_group_data".$key;
        if ($isCache) {
            $data = $this->app->cache->get($cacheKey);
            if (!empty($data)) {
                return $data;
            }
        }

        $query = SystemGroupData::alias('data')
            ->join('system_group group', 'group.id= data.group_id')
            ->where([
                "group.plugin_name" => $plugin,
                "group.name" => $key,
                "data.status" => 1,
            ]);
        $list = $query->order("data.sort asc")->select()->toArray();
        $data = [];
        foreach ($list as $item) {
            $data[] = json_decode($item['value'], true);
        }

        if ($isCache) {
            $this->app->cache->set($cacheKey, $data);
        }
        if(empty($data) && !empty($default)){
            return $default;
        }

        return $data;

    }

}
