<?php


namespace quick\admin\library\service;


use app\common\model\SystemPlugin;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use plugins\Plugin;
use quick\admin\library\cloud\CloudService;
use quick\admin\library\tools\HttpTools;
use quick\admin\QuickPluginService;
use quick\admin\Service;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use think\Exception;
use think\facade\Db;

class PluginService extends Service
{

    /**
     *  获取插件实例
     *
     * @param string $name
     * @return \plugins\Plugin
     * @throws \Exception
     */
    public function getPlugin(string $name): QuickPluginService
    {
        $path = root_path('plugins/' . $name) . "Plugin.php";
        if(!is_file($path)){
            $path = root_path('app/' . $name) . "Plugin.php";
        }

        if (is_file($path)) {
            $resource = str_replace(
                ['/', '.php'],
                ['\\', ''],
                strAfter($path, root_path())
            );
            return invoke($resource);
        } else {
            throw new \Exception('插件缺失Plugin.php文件:' . $name);
        }
    }


    /**
     * 获取插件实例
     *
     * @param string $name
     * @return \plugins\Plugin
     * @throws \Exception
     */
    public function getPluginQuickService(string $name)
    {
        $path = root_path('plugins/' . $name) . "QuickService.php";
        if (is_file($path)) {
            $resource = str_replace(
                ['/', '.php'],
                ['\\', ''],
                strAfter($path, root_path())
            );
            return invoke($resource);
        }
    }


    /**
     * 应用启动加载
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function bootPlugins()
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $plugin){
            $plugin = $this->getPlugin($plugin['name']);
            $plugin->runBoot();
        }
    }


    /**
     * 加载插件
     *
     * @param array $names
     */
    public function registerPlugins(array $names)
    {
        foreach ($names as $pluginName) {
            try {
                $plugin = $this->getPlugin($pluginName);
                $plugin->initPlugin();

                /** 兼容 内容跟plugin重复 */
                $pluginQuickService = $this->getPluginQuickService($pluginName);
                if ($pluginQuickService) {
                    $pluginQuickService->boot();
                }

            } catch (\Exception $e) {
                continue;
            }
        }
    }


    /**
     * @param array $names
     * @throws \Exception
     */
    public function readerPluginComponent()
    {
        $plugins = $this->getPlugins();
        foreach ($plugins as $plugin) {

            /** @var QuickPluginService $plugin */
            $plugin = $this->getPlugin($plugin['name']);
            if ($plugin) {
                $plugin->readerComponent();
            }

        }
    }


    /**
     * @param string $name
     * @throws \Exception
     */
    public function readerPluginComponentByName(string $name)
    {
        $plugin = $this->getPlugin($name);
        if ($plugin) {
            $plugin->readerComponent();
        }
    }



    /**
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getPlugins()
    {
        static $plugins = [];
        if(empty($plugins)){
            $model = app()->db->name('SystemPlugin');
            if(!$this->app->isDebug()){
                $model->cache("_quick_plugins");
            }
            $plugins = $model->where([
                    'is_deleted' => 0,
                    'status' => 1,
                ])->select()->toArray();
        }
        return $plugins;
    }


    /**
     * 下载插件
     * @param string $name
     * @param $data
     * @return bool|string
     */
    public function download(string $name = '', $data = [])
    {

        $data = array_merge($data, [
            'name' => $name,
        ]);
        $name = $name."-".date('YmdHis');
        $res = CloudService::instance()->pluginDownload($data);

        $tmpFile = $this->getTempDir() . "{$name}.zip";
        if ($write = fopen($tmpFile, 'w')) {
            fwrite($write, $res);
            fclose($write);
            return $tmpFile;
        }
        return $tmpFile;
    }


    /**
     * @param string $name
     * @param bool $force
     * @param array $extend
     * @throws \Exception
     */
    public function install(string $name, bool $force = false, array $extend = [])
    {
        $pluginDir = $this->getPluginDir($name);
        if (!$name || (is_dir($pluginDir) && !$force)) {
            throw new \Exception('Plugin already exists');
        }

        $zip = $this->download($name, $extend);

        try {

            $this->unzip($zip, $pluginDir);
            $this->checkPlugin($name);

            if (!$force) {
                $this->checkFileDiff($name);
            }

        } catch (\Exception $e) {

            @rmdirs($pluginDir);
            throw new \Exception($e->getMessage());
        } finally {
            @unlink($zip);
        }


        Db::startTrans();
        try {

            $plugin = $this->getPlugin($name);
            $plugin->install();
            $info = pluginInfo($name);
            SystemPlugin::create([
                'name' => $name,
                'display_name' => $info['display_name'] ?? $name,
                'desc' => $info['desc'] ?? $name,
                'status' => 0,
                'is_deleted' => 0,
                'version' => $info['version'] ?? '1.0.0',
            ]);
            Db::commit();
        } catch (Exception $e) {
            @rmDirs($pluginDir);
            Db::rollback();
            throw new \Exception($e->getMessage());
        }

        // 导入
        $this->importSql($name);

        // 启用插件
        $this->enable($name, true);

    }


    /**
     * 导入SQL
     * @param string $name 插件
     * @return bool
     */
    public function importSql(string $name): bool
    {
        $sqlFile = $this->getPluginDir($name) . 'install.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 2) == '/*') {
                    continue;
                }

                $tempLine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    $tempLine = str_ireplace('__PREFIX__', config('database.prefix'), $tempLine);
                    $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                    try {
                        Db::execute($tempLine);
                    } catch (\PDOException $e) {
                        //$e->getMessage();
                    }
                    $tempLine = '';
                }
            }
        }
        return true;
    }

    /**
     * @param string $pluginName
     * @param bool $force
     * @throws \Exception
     */
    public function uninstall(string $pluginName, $force = false)
    {
        if (!$pluginName || !is_dir($this->getPluginDir($pluginName))) {
            throw new \Exception('Plugin not exists');
        }
        if (!$force) {
            $this->checkFileDiff($pluginName);
        }


        // 移除插件全局资源文件
        if ($force) {
            $list = $this->getGlobalFiles($pluginName);
            foreach ($list as $k => $v) {
                @unlink($this->rootPath() . $v);
            }
        }

        $plugin = $this->getPlugin($pluginName);
        if (method_exists($plugin, "uninstall")) {
            $plugin->uninstall();
        }
        SystemPlugin::destroy(['name' => $pluginName]);
        // 移除插件目录
        rmdirs($this->getPluginDir($pluginName));

    }



    /**
     * @param $files
     * @param $savePath
     * @return bool
     * @throws \Exception
     */
    public function backupFiles($files, $savePath)
    {
        $zip = new ZipFile();
        try {
            foreach ($files as $k => $v) {
                $zip->addFile($this->rootPath() . $v, $v);
            }
            $zip->saveAsFile($savePath);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $zip->close();
        }
        return true;
    }


    /**
     * 备份插件
     * @param string $name
     * @throws \Exception
     */
    public function backupPlugin(string $name)
    {
        $pluginBackupDir = $this->getPluginBackupDir();
        $file = $pluginBackupDir . $name . '-backup-' . date("YmdHis") . '.zip';
        $zipFile = new ZipFile();
        try {
            $zipFile
                ->addDirRecursive($this->getPluginDir($name))
                ->saveAsFile($file)
                ->close();
        } catch (ZipException $e) {
            throw new \Exception($e->getMessage());
        } finally {
            $zipFile->close();
        }

    }


    /**
     * 获取插件备份目录
     */
    public function getPluginBackupDir()
    {
        $dir = $this->rootPath() . "runtime" . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }


    /**
     * 启用插件
     * @param string $name
     * @param bool $force
     * @throws \Exception
     */
    public function enable(string $name, $force = false)
    {
        if (!$name || !is_dir($this->getPluginDir($name))) {
            throw new \Exception('Plugin not exists');
        }
        if (!$force) {
            $this->checkFileDiff($name);
        }

        //备份冲突文件
        if (config('quick.backup_global_files')) {
            $diffFiles = $this->getGlobalFiles($name, true);
            $backupPath = $this->getTempDir() . $name . "-diff-enable-" . date("YmdHis") . ".zip";
            $this->backupFiles($diffFiles, $backupPath);
        }

        $files = $this->getGlobalFiles($name);
        if ($files) {
            $this->config($name, ['files' => $files]);
        }

        //  全局文件处理 start

        $pluginRootDir = $this->getPluginDir($name);
        $sourceAssetsDir = $this->getSourceAssetsDir($name);
        $destAssetsDir = $this->getPluginAssetsDir($name);

        if (is_dir($sourceAssetsDir)) {
            copydirs($sourceAssetsDir, $destAssetsDir);
        }
        foreach ($this->globalDirs() as $k => $dir) {
            if (is_dir($pluginRootDir . $dir)) {
                copydirs($pluginRootDir . $dir, $this->rootPath() . $dir);
            }
        }

        //插件纯净模式时将插件目录下的app、public和assets删除
        if (config('quick.plugin_mode')) {

            // 备份全局文件
            $globalFiles = $this->getGlobalFiles($name);
            if ($globalFiles) {
                $backupPath = $this->getTempDir() . $name . "-global-files.zip";
                $this->backupFiles($globalFiles, $backupPath);
            }
            // 删除插件目录已复制到全局的文件
            @rmdirs($sourceAssetsDir);
            foreach ($this->globalDirs() as $k => $dir) {
                @rmdirs($pluginRootDir . $dir);
            }
        }
        //  全局文件处理 end


        SystemPlugin::update(['status' => 1],['name' => $name]);
        // 执行插件enable
        $plugin = $this->getPlugin($name);
        if (method_exists($plugin, "enable")) {
            $plugin->enable();
        }


    }


    /**
     * 禁用插件
     * @param string $name
     * @param bool $force
     * @throws \Exception
     */
    public function disable(string $name, $force = false)
    {

        if (!$name || !is_dir($this->getPluginDir($name))) {
            throw new \Exception('Plugin not exists');
        }


        if (!$force) {
            $this->checkFileDiff($name);
        }

        //备份
        if (config('quick.backup_global_files')) {
            $diffFiles = $this->getGlobalFiles($name, true);
            if ($diffFiles) {
                $zip = new ZipFile();
                try {
                    foreach ($diffFiles as $k => $v) {
                        $zip->addFile($this->rootPath() . $v, $v);
                    }
                    $backupDir = $this->getTempDir();
                    $zip->saveAsFile($backupDir . $name . "-diff-disable-" . date("YmdHis") . ".zip");
                } catch (\Exception $e) {
                    throw new \Exception($e->getMessage());
                } finally {
                    $zip->close();
                }
            }
        }

        $pluginRootDir = $this->getPluginDir($name);
        $sourceAssetsDir = $this->getSourceAssetsDir($name);
        $destAssetsDir = $this->getPluginAssetsDir($name);

        $globalFiles = $this->getGlobalFiles($name);

        // 备份
        if (config('quick.plugin_mode') || !$globalFiles) {
            // 获取安装时全局文件的记录
            $config = $this->config($name);
            if ($config && isset($config['files']) && is_array($config['files'])) {
                foreach ($config['files'] as $index => $item) {

                    $item = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $item);

                    if (stripos($item, str_replace($this->rootPath(), '', $destAssetsDir)) === 0) {
                        continue;
                    }
                    //检查目录是否存在，不存在则创建
                    $itemBaseDir = dirname($pluginRootDir . $item);
                    if (!is_dir($itemBaseDir)) {
                        @mkdir($itemBaseDir, 0755, true);
                    }
                    if (is_file($this->rootPath() . $item)) {
                        @copy($this->rootPath() . $item, $pluginRootDir . $item);
                    }
                }
                $globalFiles = $config['files'];
            }
            //复制插件目录资源
            if (is_dir($destAssetsDir)) {
                @copydirs($destAssetsDir, $sourceAssetsDir);
            }
        }


        $dirs = [];
        foreach ($globalFiles as $k => $v) {
            $file = $this->rootPath() . $v;
            $dirs[] = dirname($file);
            @unlink($file);
        }

        $dirs = array_filter(array_unique($dirs));
        foreach ($dirs as $k => $v) {
            remove_empty_folder($v);
        }

        SystemPlugin::update(['status' => 0],['name' => $name]);
        /** @var Plugin $plugin */
        $plugin = $this->getPlugin($name);
        if (method_exists($plugin, "disable")) {
            //执行插件disable
            $plugin->disable();
        }
    }


    /**
     * 升级插件
     *
     * @param string $pluginName
     * @throws \Exception
     */
    public function upgrade(string $pluginName)
    {

        $this->backupPlugin($pluginName);
    }


    /**
     * @param string $zipPath 压缩文件
     * @param string $saveDir 解压地址
     * @return bool
     * @throws \Exception
     */
    public function unzip(string $zipPath, string $saveDir)
    {


        // 打开插件压缩包
        $zip = new ZipFile();
        try {
            $zip->openFile($zipPath);
        } catch (ZipException $e) {
            $zip->close();
            throw new \Exception('Unable to open the zip file');
        }

        if (!is_dir($saveDir)) {
            @mkdir($saveDir, 0755);
        }

        // 解压插件压缩包
        try {
            $zip->extractTo($saveDir);
        } catch (ZipException $e) {
            throw new \Exception('Unable to extract the file');
        } finally {
            $zip->close();
        }
        return true;
    }


    /**
     * 检查插件是否正常
     *
     * @param $name
     * @return bool
     * @throws \Exception
     */
    public function checkPlugin($name)
    {

        $pluginDir = $this->getPluginDir($name);
        if (!$name || !is_dir($pluginDir)) {
            throw new \Exception('Plugin not exists');
        }

        $plugin = $this->getPlugin($name);
        if(!$plugin){
            throw new \Exception("The plugin file does not exist");
        }

        $info = pluginInfo($name);
        $info_check_keys = ['name', 'title', 'intro', 'author', 'version', 'state'];
        foreach ($info_check_keys as $value) {
            if (!array_key_exists($value, $info)) {
//                throw new \Exception("The configuration file content is incorrect");
            }
        }
        return true;
    }

    /**
     * 获取插件文件夹
     * @param string $pluginName
     * @return string
     */
    public function getPluginDir(string $pluginName)
    {
        return $this->rootPath() . "plugins" . DIRECTORY_SEPARATOR . $pluginName . DIRECTORY_SEPARATOR;
    }


    /**
     * @return string
     */
    public function getTempDir()
    {
        return $this->rootPath() . "runtime" . DIRECTORY_SEPARATOR . "admin" . DIRECTORY_SEPARATOR;
    }


    /**
     * @return string
     */
    public function rootPath()
    {
        return app()->getRootPath();
    }

    /**
     * 获取插件源资源文件夹
     * @param string $name 插件名称
     * @return  string
     */
    protected function getSourceAssetsDir(string $name)
    {
        return $this->getPluginDir($name) . 'assets' . DIRECTORY_SEPARATOR;
    }


    /**
     * @param $name
     * @return string
     */
    public function getPluginAssetsDir(string $name)
    {
        return $this->rootPath() . "public" .
            DIRECTORY_SEPARATOR . "assets" .
            DIRECTORY_SEPARATOR . "plugins" .
            DIRECTORY_SEPARATOR . $name .
            DIRECTORY_SEPARATOR;
    }


    /**
     * 检查冲突文件
     * @param $name
     * @return bool
     * @throws \Exception
     */
    public function checkFileDiff($name)
    {
        // 检测冲突文件
        $list = $this->getGlobalFiles($name, true);
        if ($list) {
            //发现冲突文件，抛出异常
            throw new \Exception('发现冲突文件');
        }
        return true;
    }


    /**
     * 全局文件夹
     *
     * @return array
     */
    protected function globalDirs(): array
    {
        return [
            'app',
            'components',
            'assets',
            'public',
        ];
    }


    /**
     * @param string $name 插件名称
     * @param bool $diff 获取差异文件
     * @return array
     */
    public function getGlobalFiles(string $name, $diff = false)
    {
        $list = [];
        $pluginDir = $this->getPluginDir($name);
        $checkDirList = $this->globalDirs();

        $assetDir = $this->getPluginAssetsDir($name);

        foreach ($checkDirList as $k => $dirName) {

            if (!is_dir($pluginDir . $dirName)) {
                continue;
            }

            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($pluginDir . $dirName, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $fileInfo) {
                if ($fileInfo->isFile()) {
                    $filePath = $fileInfo->getPathName();

                    if ($dirName === 'assets') {
                        $path = str_replace($this->rootPath(), '', $assetDir) . str_replace($pluginDir . $dirName . DIRECTORY_SEPARATOR, '', $filePath);
                    } else {
                        $path = str_replace($pluginDir, '', $filePath);
                    }
                    if ($diff) {
                        $destPath = $this->rootPath() . $path;
                        if (is_file($destPath)) {
                            if (filesize($filePath) != filesize($destPath) || md5_file($filePath) != md5_file($destPath)) {
                                $list[] = $path;
                            }
                        }
                    } else {
                        $list[] = $path;
                    }
                }
            }
        }
        $list = array_filter(array_unique($list));
        return $list;
    }


    /**
     * 读取或修改插件配置
     * @param string $pluginName
     * @param array $data
     * @return array
     */
    public function config(string $pluginName, array $data = []): array
    {
        $pluginDir = $this->getPluginDir($pluginName);
        $pluginConfigFile = $pluginDir . '.plugin';
        $config = [];
        if (is_file($pluginConfigFile)) {
            $config = (array)json_decode(file_get_contents($pluginConfigFile), true);
        }
        $config = array_merge($config, $data);
        if (!empty($data)) {
            file_put_contents($pluginConfigFile, json_encode($config, JSON_UNESCAPED_UNICODE));
        }
        return $config;
    }


    /**
     * 打包
     * @param string $pluginName
     * @throws \Exception
     */
    public function package(string $pluginName)
    {
        $pluginDir = $this->getPluginDir($pluginName);
        $infoFile = $pluginDir . 'info.ini';
        if (!is_file($infoFile)) {
            throw new \Exception(__('Plugin info file was not found'));
        }

        $plugin = $this->getPlugin($pluginName);
        if (!$plugin) {
            throw new \Exception(__('Plugin info file data incorrect'));
        }

        $pluginInfo = pluginInfo($pluginName);
        $name = isset($pluginInfo['name']) ? $pluginInfo['name'] : '';
        if (!$pluginInfo || !preg_match("/^[a-z]+$/i", $name) || $pluginName != $name) {
            echo $name;
            throw new Exception(__('Plugin info name incorrect'));
        }

        $pluginVersion = isset($pluginInfo['version']) ? $pluginInfo['version'] : '';
        if (!$pluginVersion || !preg_match("/^\d+\.\d+\.\d+$/i", $pluginVersion)) {
            throw new \Exception(__('Plugin info version incorrect'));
        }

        $pluginTemDir = $this->getTempDir() .'packages'.DIRECTORY_SEPARATOR;
        if (!is_dir($pluginTemDir)) {
            @mkdir($pluginTemDir, 0755, true);
        }
        $pluginFile = $pluginTemDir . $pluginName . '-' . $pluginVersion . '.zip';
        if (!class_exists('ZipArchive')) {
            throw new \Exception(__('ZinArchive not install'));
        }
        $zip = new \ZipArchive;
        $zip->open($pluginFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pluginDir), \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', substr($filePath, strlen($pluginDir)));
                if (!in_array($file->getFilename(), ['.git', '.DS_Store', 'Thumbs.db','node_modules'])) {
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }
        $zip->close();
        echo $pluginFile.PHP_EOL;
        echo "Package successfully!";
    }


    /**
     * 获取 plugins 路径
     * @return string
     */
    public function getPluginsPath()
    {
        // 初始化插件目录
        $plugins_path = $this->app->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR;
        // 如果插件目录不存在则创建
        if (!is_dir($plugins_path)) {
            @mkdir($plugins_path, 0755, true);
        }

        return $plugins_path;
    }


    public function load()
    {
        foreach (glob($this->getPluginsPath() . '*/*.php') as $plugins_file) {
            // 格式化路径信息
            $info = pathinfo($plugins_file);
            // 获取插件目录名
            $name = pathinfo($info['dirname'], PATHINFO_FILENAME);
            // 找到插件入口文件
            if (strtolower($info['filename']) === 'plugin') {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\" . $info['filename']);

            }
        }
    }
}
