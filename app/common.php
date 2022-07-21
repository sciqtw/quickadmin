<?php
// 应用公共文件

use quick\admin\library\service\SystemService;
use quick\Sys;
use think\Container;
use think\facade\Config;
use think\facade\Lang;


if (!function_exists('sys')) {

    /**
     * @return Sys
     */
    function sys()
    {
        return Container::getInstance()->make(\quick\Sys::class);
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
     * @param string $subject
     * @param string $search
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
     * @param array $vars 动态变量值
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
     * @param string $value
     * @param int $limit
     * @param string $end
     * @return string
     */
    function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }

        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }
}



if (!function_exists('setPluginInfo')) {

    /**
     * 设置插件配置
     * @param string $pluginName
     * @param array $array
     * @return bool
     * @throws Exception
     */
    function setPluginInfo(string $pluginName,array $array)
    {

        $file = app()->getRootPath() . "plugins" . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR."info.ini";
        $data = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $data[] = "[$key]";
                foreach ($val as $skey => $sval) {
                    $data[] = "$skey = " . (is_numeric($sval) ? $sval : $sval);
                }
            } else {
                $data[] = "$key = " . (is_numeric($val) ? $val : $val);
            }
        }

        if (file_put_contents($file, implode("\n", $data) . "\n", LOCK_EX)) {
           return true;
        } else {
            throw new Exception("文件没有写入权限");
        }
    }
}


if (!function_exists('sysConfig')) {

    /**
     * 获取或配置系统参数
     *
     * @param string $name
     * @param null $value
     * @param string $plugin
     * @return array|int|mixed|string
     */
    function sysConfig($name = '', $value = null,string $plugin = 'admin')
    {
        try {
            return SystemService::instance()->get($name,$value,$plugin);
//            return SystemService::instance()->set($name, $value,$plugin);
        }catch (\Exception $e){
            return '';
        }

    }
}


if (!function_exists('sysGroupData')) {

    /**
     * 获取分组数据
     *
     * @param string $key
     * @param string $plugin
     * @param array $default
     * @return array|mixed
     */
    function sysGroupData(string $key, string $plugin = 'admin', array $default = [])
    {
        try {
            return SystemService::instance()->getGroupData($key,$plugin,$default);
        }catch (\Exception $e){
            return [];
        }

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



if (!function_exists('mb_ucfirst')) {
    function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)) . mb_strtolower(mb_substr($string, 1));
    }
}


if (!function_exists('copyDirs')) {

    /**
     * 复制文件夹
     * @param string $source 源文件夹
     * @param string $dest 目标文件夹
     */
    function copyDirs(string $source, string $dest)
    {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        foreach (
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            ) as $item
        ) {
            if ($item->isDir()) {
                $sonDir = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
                if (!is_dir($sonDir)) {
                    mkdir($sonDir, 0755, true);
                }
            } else {
                copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}


if (!function_exists('rmDirs')) {

    /**
     * 删除文件夹
     * @param string $dirname 目录
     * @param bool $withSelf 是否删除自身
     * @return boolean
     */
    function rmDirs(string $dirname,bool $withSelf = true)
    {
        if (!is_dir($dirname)) {
            return false;
        }
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirname, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileInfo) {
            $todo = ($fileInfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileInfo->getRealPath());
        }
        if ($withSelf) {
            @rmdir($dirname);
        }
        return true;
    }
}


if (!function_exists('remove_empty_folder')) {
    /**
     * 移除空目录
     * @param string $dir 目录
     */
    function remove_empty_folder($dir)
    {
        try {
            $isDirEmpty = !(new \FilesystemIterator($dir))->valid();
            if ($isDirEmpty) {
                @rmdir($dir);
                remove_empty_folder(dirname($dir));
            }
        } catch (\UnexpectedValueException $e) {

        } catch (\Exception $e) {

        }
    }

}


if (!function_exists('encode')) {
    /**
     * 加密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function encode(string $content): string
    {
        [$chars, $length] = ['', strlen($string = iconv('UTF-8', 'GBK//TRANSLIT', $content))];
        for ($i = 0; $i < $length; $i++) $chars .= str_pad(base_convert(ord($string[$i]), 10, 36), 2, 0, 0);
        return $chars;
    }
}
if (!function_exists('decode')) {
    /**
     * 解密 UTF8 字符串
     * @param string $content
     * @return string
     */
    function decode(string $content): string
    {
        $chars = '';
        foreach (str_split($content, 2) as $char) {
            $chars .= chr(intval(base_convert($char, 36, 10)));
        }
        return iconv('GBK//TRANSLIT', 'UTF-8', $chars);
    }
}

if (!function_exists('distance')) {
    /**
     * 求两个已知经纬度之间的距离,单位为米
     *
     * @param $lng1 Number 位置1经度
     * @param $lat1 Number 位置1纬度
     * @param $lng2 Number 位置2经度
     * @param $lat2 Number 位置2纬度
     * @return float 距离，单位米
     */
    function distance($lng1, $lat1, $lng2, $lat2)
    {
        // 将角度转为狐度
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;
        return $s;
    }
}
