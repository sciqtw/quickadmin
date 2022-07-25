<?php
declare (strict_types = 1);

use quick\admin\exceptions\AdminException;
use quick\admin\library\service\SystemService;
use think\facade\Config;
use think\facade\Lang;


if(!function_exists('filter_params')){

    function filter_params($name = '')
    {
        $filterParam = request()->param("filters");
        $filterParam = $filterParam ? json_decode(urldecode(base64UrlDecode($filterParam)), true) : [];
        $data = request()->param();
        unset($data['filters']);
        $filterParam = array_merge($data,$filterParam);
        if(!empty($name)){
            return isset($filterParam[$name]) ? $filterParam[$name]:'';
        }
        return $filterParam;
    }
}

if (!function_exists('quick_abort')) {

    /**
     * @param int $code
     * @param string $msg
     * @param array $data
     * @throws \quick\admin\Exception
     */
    function quick_abort($code = 200, $msg = '',$data = [])
    {
        throw new \quick\admin\Exception($msg,$code,$data);
    }
}


/**
 *  转码bae64
 */
if (!function_exists('base64UrlEncode')) {

    function base64UrlEncode($input)
    {
        return strtr(base64_encode($input), '+/', '-_');
    }
}


/**
 *  解码base64
 */
if (!function_exists('base64UrlDecode')) {

    function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}


if (!function_exists('availableStyles')) {

    function availableStyles()
    {
        return \quick\admin\quick::availableStyles(app("request"));
    }
}

if (!function_exists('availableScripts')) {

    function availableScripts()
    {
        return \quick\admin\quick::availableScripts(app("request"));
    }
}


if (!function_exists('jsonVariables')) {

    function jsonVariables()
    {
        return json_encode(\quick\admin\quick::jsonVariables(app("request")));
    }
}


if (!function_exists('selectOptions')) {
    /**
     * @param array $array
     * @param string $value select值
     * @param string $label 选项名称
     * @return array
     */
    function selectOptions(array $array, string $value, string $label)
    {
        $options = collect($array)->each(function ($item) use ($value, $label) {
            return [
                'value' => $item[$value],
                'label' => $item[$label],
            ];
        });
        return $options;
    }
}


if (!function_exists('strAfter')) {
    /**
     * 返回给定值第一次出现后字符串的剩余部分
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    function strAfter($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}


if (!function_exists('__')) {

    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array  $vars 动态变量值
     * @param string $lang 语言
     * @return mixed
     */
    function __($name, $vars = [], $lang = '')
    {

        return Lang::get($name, $vars, $lang);
    }
}

if (!function_exists('limit')) {

    /**
     * Limit the number of characters in a string.
     *
     * @param  string  $value
     * @param  int     $limit
     * @param  string  $end
     * @return string
     */
    function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')).$end;
    }
}

if(!function_exists('plugin')){

    /**
     * @param string $plugin
     * @return \plugins\Plugin|\quick\admin\QuickPluginService|string
     * @throws Exception
     */
    function plugin(string $plugin)
    {
        $service = \quick\admin\library\service\PluginService::instance();
        $plugin = $service->getPlugin($plugin);
        return $plugin;
    }
}



if(!function_exists('pluginInfo')){

    /**
     * @param string $plugin
     * @param array $data
     * @return array
     */
    function pluginInfo(string $plugin,array $data = [])
    {
        $appPath = root_path("app/{$plugin}");
        if(!is_dir($appPath)){
            $appPath = root_path("plugins/{$plugin}");
            if(!is_dir($appPath)){
                return $data;
            }
        }
        $info_file = $appPath."info.php";
        if (is_file($info_file)) {
            $info = Config::load($info_file, $plugin);
            return array_merge($info,$data);
        }
        return $data;
    }
}



//if (!function_exists('pluginInfo')) {
//
//    /**
//     * @param string $plugin
//     * @param array $data
//     * @return array
//     */
//    function pluginInfo(string $plugin, array $data = [])
//    {
//        $appPath = root_path("app\\{$plugin}");
//        if (!is_dir($appPath)) {
//            $appPath = root_path("plugins\\{$plugin}");
//            if (!is_dir($appPath)) {
//                return $data;
//            }
//        }
//        $info_file = $appPath . "info.ini";
//        if (is_file($info_file)) {
//            $info = parse_ini_file($info_file, true, INI_SCANNER_TYPED) ?: [];
//            return array_merge($info, $data);
//        }
//        return $data;
//    }
//}



if (!function_exists('readerPluginComponentByName')) {

    /**
     * @param string $name
     * @throws Exception
     */
    function readerPluginComponentByName(string $name)
    {
        \quick\admin\library\service\PluginService::instance()->readerPluginComponentByName($name);
    }
}



if (!function_exists('renderPluginsComponents')) {

    /**
     * @throws Exception
     */
    function renderPluginsComponents()
    {
        \quick\admin\library\service\PluginService::instance()->readerPluginComponent();
    }
}

if(!function_exists('saveDataAuth')){

    /**
     * 数据持久化权限
     * @throws Exception
     */
    function saveDataAuth()
    {
        if(env('app.app_mode') == 'demo'){
            throw new \Exception('测试系统不允许修改数据');
        }
    }
}
