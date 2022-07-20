<?php
declare (strict_types = 1);

namespace quick\admin\library\tools;


class TreeArray
{
    /**
     * 生成数组树的二维数组
     *
     * @var array
     */
    public $arr = [];

    /**
     * @var
     */
    protected static $instance;

    /**
     * 实例化
     *
     * @param array $options
     * @return static
     */
    public static function instance($arr = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($arr);
        }

        return self::$instance;
    }


    /**
     * 一维数据数组生成数据树 返回多维数组
     * @param array $list 数据列表
     * @param string $id 父ID Key
     * @param string $pid ID Key
     * @param string $son 定义子数据Key
     * @return array
     */
    public static function arr2tree(array $list, $id = 'id', $pid = 'pid', $son = 'sub')
    {
        list($tree, $map) = [[], []];
        foreach ($list as $k =>$item){
            if(is_object($item) && method_exists($item,"toArray")){
                $list[$k] = $item = $item->toArray();
            }
            $map[$item[$id]] = $item;
        }

        foreach ($list as $item){

            if (isset($item[$pid]) && isset($map[$item[$pid]])) {
                $map[$item[$pid]][$son][] = &$map[$item[$id]];
            } else {
                $tree[] = &$map[$item[$id]];
            }
        }
        unset($map);
        return $tree;
    }

    /**
     * 一维数据数组生成数据树 返回一维数组
     * @param array $list 数据列表
     * @param string $id ID Key
     * @param string $pid 父ID Key
     * @param string $path
     * @param string $ppath
     * @return array
     */
    public static function arr2table(array $list, $id = 'id', $pid = 'pid', $path = 'path', $ppath = '')
    {
        $tree = [];
        $arrs = self::arr2tree($list, $id, $pid);
        foreach ($arrs as $attr) {
            $attr[$path] = "{$ppath}-{$attr[$id]}";
            $attr['sub'] = isset($attr['sub']) ? $attr['sub'] : [];
            $attr['spt'] = substr_count($ppath, '-');
            $attr['spl'] = str_repeat("　├　", $attr['spt']);
            $sub = $attr['sub'];
            $attr['sub_count'] = count($attr['sub']);
            unset($attr['sub']);
            $tree[] = $attr;
            if (!empty($sub)) $tree = array_merge($tree, self::arr2table($sub, $id, $pid, $path, $attr[$path]));
        }
        return $tree;
    }

    /**
     * 获取数据树子ID
     * @param array $list 数据列表
     * @param int $id 起始ID
     * @param string $key 子Key
     * @param string $pkey 父Key
     * @return array
     */
    public static function getArrSubIds($list, $id = 0, $key = 'id', $pkey = 'pid')
    {
        $ids = [intval($id)];
        foreach ($list as $vo) if (intval($vo[$pkey]) > 0 && intval($vo[$pkey]) === intval($id)) {
            $ids = array_merge($ids, self::getArrSubIds($list, intval($vo[$key]), $key, $pkey));
        }
        return $ids;
    }

}
