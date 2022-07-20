<?php
declare (strict_types=1);

namespace quick\admin\library\service;


use app\common\model\SystemMenu;
use quick\admin\contracts\AuthInterface;
use quick\admin\library\tools\TreeArray;
use quick\admin\Quick;
use quick\admin\Service;

/**
 * Class MenuService
 * @package quick\librarys
 */
class MenuService extends Service
{


    private $authService;

    /**
     * @param string $plugin
     * @param bool $auth
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTreeMenus(string $plugin, bool $auth = true)
    {

        $menus = $this->menus = app()->db->name('SystemMenu')
            ->where(['status' => 1, 'plugin_name' => $plugin])
            ->order("sort desc,id desc")
            ->select()->toArray();

        return $this->buildMenu(TreeArray::arr2tree($menus, 'id', 'pid', 'children'), NodeService::instance()->getNodes(), $auth);
    }


    /**
     * @param string $plugin
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenusList(string $plugin = '')
    {
        $plugin = $plugin ?: app()->http->getName();
        $nodes = $this->app->db->name('SystemNode')
            ->field("node as value,node as title")
            ->where([
                "status" => 1,
                "plugin_name" => $plugin,
                "is_menu" => 1
            ])->select();
        return $nodes;
    }


    /**
     * @param array $vo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getTable($vo = [])
    {
        $menus = app()->db->name('SystemMenu')->where(['status' => '1'])->order('sort desc,id asc')->select()->toArray();
        !empty($vo) && $menus[] = $vo;
        foreach ($menus = TreeArray::arr2table($menus, 'id', 'pid', 'url_key') as $key => &$menu) {
            if (substr_count($menu['url_key'], '-') > 3) unset($this->menus[$key]); # 移除三级以下的菜单
            elseif (isset($vo['pid']) && $vo['pid'] !== '' && $cur = "-{$vo['pid']}-{$vo['id']}") {
                if (stripos("{$menu['url_key']}-", "{$cur}-") !== false || $menu['url_key'] === $cur) unset($this->menus[$key]); # 移除与自己相关联的菜单
            }
        }
        return $menus;
    }


    /**
     * @param array $vo
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getMenuOptions($vo = [])
    {
        $menus = $this->getTable(['title' => '顶级菜单', 'id' => 0, 'pid' => '-1']);
        $menus = collect($menus)->each(function ($menu) use (&$data) {
            return [
                'value' => $menu['id'],
                'label' => $menu['spl'] . $menu['title'],
            ];
        });
        return $menus->toArray();
    }


    /**
     * 构建系统菜单 并过滤权限节点
     *
     * @param array $menus
     * @param array $nodes
     * @param bool $auth
     * @return array
     * @throws \ReflectionException
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    private function buildMenu(array $menus, array $nodes, bool $auth = true): array
    {
        if(!$this->authService){
            $this->authService = Quick::getAuthService();
        }
        $authService = $this->authService;

        foreach ($menus as $key => &$menu) {

            $menu['name'] = $menu['title'];
            $meta = [
                'title' => $menu['title'],
                'target' => $menu['target'],
                'icon' => $menu['icon'],
                'badge' => $menu['badge'],
            ];

            if (!empty($menu['children'])) {
                $menu['children'] = $this->buildMenu($menu['children'], $nodes);
            } else {
                $menu['children'] = [];
            }

            if (!empty($menu['children'])) {
                $menu['path'] = '#' . $menu['id'];
            } elseif ($menu['path'] === '#') {
                unset($menus[$key]);
            } elseif (preg_match('|^https?://|i', $menu['path'])) {
                if (!empty($menu['node']) && !$authService->check($menu['node'])) {
                    unset($menus[$key]);
                } elseif ($menu['params']) {
                    $menu['path'] .= (strpos($menu['path'], '?') === false ? '?' : '&') . $menu['params'];
                }
            } elseif (!empty($menu['node']) && !$authService->check($menu['node'], false)) {
                unset($menus[$key]);
            } else {
                $node = join('/', array_slice(explode('/', $menu['path']), 0, 4));
                $menu['path'] = "/" . $node . (empty($menu['params']) ? '' : "?{$menu['params']}");
                if (!$authService->check($node) && $auth) {
                    unset($menus[$key]);
                }
            }

            if ($menu['path'] == '/') {
                $menu['meta']['affix'] = true;
            }
            $menu['meta'] = $meta;
        }
        return array_merge($menus);
    }


    /**
     * 禁用插件菜单
     * @param string $plugin_name
     * @return bool|\think\Model
     */
    public function disable(string $plugin_name)
    {
        $ids = $this->getMenusIdsByName($plugin_name);
        if (empty($ids)) {
            return true;
        }
        return SystemMenu::where("id", 'in', $ids)->update(['status' => 0]);
    }

    /**
     * 启用插件菜单
     * @param string $plugin_name
     * @return bool|\think\Model
     */
    public function enable(string $plugin_name)
    {
        $ids = $this->getMenusIdsByName($plugin_name);
        if (empty($ids)) {
            return true;
        }
        return SystemMenu::where("id", 'in', $ids)->update(['status' => 1]);
    }


    /**
     * @param array $menus
     * @param array $oldMenus
     * @param int $pid
     * @param string $plugin_name
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function create(array $menus,array $oldMenus = [],$pid = 0,$plugin_name = 'admin')
    {
        if (!is_numeric($pid)) {
            $parentRule = SystemMenu::where("path","like",$pid)->find();
            $parentId = $parentRule ? $parentRule['id'] : 0;
        } else {
            $parentId = $pid;
        }
        $allow = array_flip(['path', 'title',  'icon', 'plugin_name', 'node', 'sort','params','target']);
        foreach ($menus as $k => $val) {
            $menu = array_intersect_key($val, $allow);
            if(!isset($menu['plugin_name']) && !empty($plugin_name)){
                $menu['plugin_name'] = $plugin_name;
            }
            $menu['pid'] = $parentId;
            if(!isset($oldMenus[$menu['path']])){
                $newMenu = SystemMenu::create($menu);
            }else{
                $menu = $oldMenus[$menu['path']];
                $newMenu = SystemMenu::update($menu,['id' => $menu['id']]);
            }
            if(isset($val['children']) && !empty($val['children'])){
                $this->create($val['children'],$oldMenus,$newMenu['id'],$plugin_name);
            }
        }
    }


    /**
     * @param string $plugin_name
     * @return bool
     */
    public function delete(string $plugin_name)
    {
        $ids = $this->getMenusIdsByName($plugin_name);
        if (empty($ids)) {
            return true;
        }
        return SystemMenu::destroy($ids);
    }

    /**
     * 根据插件名称获取插件所有菜单id
     * @param string $name
     * @return array
     */
    protected function getMenusIdsByName(string $name)
    {
        return SystemMenu::where('path', 'like', "{$name}%")->column("id");
    }
}
