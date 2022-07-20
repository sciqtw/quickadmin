<?php


declare (strict_types=1);

namespace quick\admin\command;


use quick\admin\library\service\FileService;
use quick\admin\library\service\PluginService;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Exception;
use think\helper\Str;

/**
 * Class PluginCommand
 * @package quick\admin\command
 */
class PluginCommand extends Command
{


    /**
     * 任务编号
     * @var string
     */
    protected $code;
    protected $name = "quick:plugin";

    protected $input = null;

    /**
     * 配置指令参数
     */
    public function configure()
    {
        $this->setName($this->name);
        $this->addArgument('name', Argument::REQUIRED, 'Plugin name');
        $this->addOption('action', 'c', Option::VALUE_REQUIRED, 'action(create/enable/disable/install/uninstall/refresh/upgrade/package/move)', 'create');
        $this->addOption('force', 'f', Option::VALUE_OPTIONAL, 'force override', null);
        $this->addOption('release', 'r', Option::VALUE_OPTIONAL, 'Plugin release version', null);
        $this->setDescription('Asynchronous Command Queue Task for ThinkAdmin');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int|null
     * @throws Exception
     */
    public function execute(Input $input, Output $output)
    {
        $this->input = $input;

        $pluginName = $input->getArgument('name');
        $action = $input->hasOption('action') ? $input->getOption('action') : 'create';

        if (method_exists($this, $method = "{$action}Action")) return $this->$method();
        $this->output->error("># Wrong operation, Allow create/enable/disable/install/uninstall/refresh/upgrade/package/move");
    }


    protected function createAction()
    {
        $pluginDir = $this->pluginPath();
        $force = $this->input->getOption('force') ? true : false;

        if (!$force && is_dir($pluginDir)) {
            throw new Exception("addon already exists!\nIf you need to create again, use the parameter --force=true ");
        }

        (new FileService())->deleteDirectory($pluginDir);
        (new FileService())->copyDirectory(
            __DIR__ . '/plugin-stubs',
            $pluginDir
        );

        $moveList = [
            'config/quick.stub',
            'controller/admin/index.stub',
            'controller/api/index.stub',
            'controller/ApiController.stub',
            "controller/BackendController.stub",
            'controller/index.stub',
            'service/BaseService.stub',
            'quick/resource/index.stub',
            'route/route.stub',
            'Plugin.stub',
            'info.stub',
        ];
        $pluginName = $this->pluginName();
        $pluginNameStudly = Str::studly($pluginName);
        foreach ($moveList as $item) {
            $formPath = $pluginDir . $item;
            $toPath = str_replace([
                ".stub",
                "ApiController",
                "BackendController",
            ], [
                ".php",
               "ApiController",
               "BackendController",
            ], $formPath);

            (new FileService())->move($formPath, $toPath);

            $this->replace([
                "{{ pluginName }}",
                "{{ pluginNameStudly }}",
            ], [
                $pluginName,
                $pluginNameStudly,
            ], $toPath);
        }


    }


    /**
     * 安装
     */
    public function installAction()
    {
        $pluginName = $this->pluginName();
        $force = $this->input->getOption('force') ? true : false;
        try {
            PluginService::instance()->install($pluginName, $force);
            $this->output->info("install {$pluginName}  successfully");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }

    }


    /**
     * 打包
     */
    public function packageAction()
    {
        $pluginName = $this->pluginName();
        $force = $this->input->getOption('force') ? true : false;
        try {
            PluginService::instance()->package($pluginName, $force);
            $this->output->info("package {$pluginName}  successfully");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }

    }


    /**
     * 卸载
     */
    public function uninstallAction()
    {
        $pluginName = $this->pluginName();
        $force = $this->input->getOption('force') ? true : false;
        try {
            PluginService::instance()->uninstall($pluginName, $force);
            $this->output->info("uninstall {$pluginName}  successfully");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }

    }


    /**
     * 启用
     */
    public function enableAction()
    {
        $pluginName = $this->pluginName();
        $force = $this->input->getOption('force') ? true : false;
        try {
            PluginService::instance()->enable($pluginName, $force);
            $this->output->info("enable {$pluginName}  successfully");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }


    }


    /**
     * 禁用插件
     */
    public function disableAction()
    {
        $pluginName = $this->pluginName();
        $force = $this->input->getOption('force') ? true : false;
        try {
            PluginService::instance()->disable($pluginName, $force);
            $this->output->info("disable {$pluginName}  successfully");
        } catch (\Exception $e) {
            $this->output->error($e->getMessage());
        }

    }


    /**
     * Replace the given string in the given file.
     *
     * @param string|array $search
     * @param string|array $replace
     * @param string $path
     * @return void
     */
    protected function replace($search, $replace, string $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }


    /**
     * Get the path to the tool.
     *
     * @return string
     */
    protected function pluginPath()
    {
        return root_path('plugins/' . $this->pluginName());
    }


    /**
     * @return string;
     */
    protected function pluginName()
    {
        return $this->argument('name');
    }


    /**
     * @param null $key
     * @return array|mixed|Argument[]
     */
    public function argument($key = null)
    {
        if (is_null($key)) {
            return $this->input->getArguments();
        }

        return $this->input->getArgument($key);
    }

}
