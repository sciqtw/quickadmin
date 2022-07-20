<?php


declare (strict_types=1);

namespace quick\admin\command;


use quick\admin\library\service\FileService;
use quick\admin\library\service\ModuleService;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

/**
 * Class ActionCommand
 * @package quick\admin\command
 */
class InstallCommand extends Command
{
    protected $type = "resource";

    private $name;


    /**
     * 查询规则
     * @var array
     */
    protected $rules = [];

    /**
     * 忽略规则
     * @var array
     */
    protected $ignore = [];


    public static function bind()
    {
        return [
            'admin'  => [
                'rules'  => [
                    'app/admin',
                    'app/common',
                    'app/common.php',
                    'components/admin',
                    'components/vcharts',
                ],
                'ignore' => [],
            ],
            'plugins'  => [
                'rules'  => [
                    'plugins/crud',
                    'plugins/Plugin.php',
                ],
                'ignore' => [],
            ],
            'quick'  => [
                'rules'  => [ 'extend/quick'],
                'ignore' => [],
            ],
            'config' => [
                'rules'  => [
                    'config/app.php',
                    'config/log.php',
                    'config/route.php',
                    'config/trace.php',
                    'config/view.php',
                    'public/index.php',
                    'public/router.php',
                ],
                'ignore' => [],
            ],
            'static' => [
                'rules'  => [
                    'public/vue3',
                    'public/assets/unpkg',
                ],
                'ignore' => [],
            ],
            'view' => [
                'rules'  => [
                    'view/quick',
                ],
                'ignore' => [],
            ],
        ];
    }



    protected function configure()
    {
        $this->setName('quick:install')
            ->addArgument('name', Argument::OPTIONAL, 'ModuleName', '')
            ->setDescription('Source code Install and Update for QuickAdmin');
    }



    /**
     * 任务执行入口
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
//        $this->name = trim($input->getArgument('name'));
        $this->installFile() && $this->installData();
    }

    /**
     * 安装本地文件
     * @return boolean
     */
    private function installFile(): bool
    {
        $module = ModuleService::instance();
        $routes = ModuleService::bind();
        $rules = [];
        $ignore = [];
        foreach ($routes as $bind) {
            $rules = array_merge($rules, $bind['rules']);
            $ignore = array_merge($ignore, $bind['ignore']);
        }

        $data = $module->grenerateDifference($rules, $ignore);
        if (empty($data)) {
            $this->output->writeln('No need to update the file if the file comparison is consistent');
            return false;
        }
        [$total, $count] = [count($data), 0];
        foreach ($data as $file) {
            [$state, $mode, $name] = $module->updateFileByDownload($file);
            if ($state) {
                if ($mode === 'add') $this->queue->message($total, ++$count, "--- {$name} add successfully");
                if ($mode === 'mod') $this->queue->message($total, ++$count, "--- {$name} update successfully");
                if ($mode === 'del') $this->queue->message($total, ++$count, "--- {$name} delete successfully");
            } else {
                if ($mode === 'add') $this->queue->message($total, ++$count, "--- {$name} add failed");
                if ($mode === 'mod') $this->queue->message($total, ++$count, "--- {$name} update failed");
                if ($mode === 'del') $this->queue->message($total, ++$count, "--- {$name} delete failed");
            }
        }
        return true;
    }

    /**
     * 安装数据库
     * @return boolean
     */
    protected function installData(): bool
    {
        return true;
    }



}
