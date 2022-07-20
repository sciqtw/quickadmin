<?php

// +----------------------------------------------------------------------
// | ProcessService 来源于开源项目ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace quick\admin\library\service;

use quick\admin\Quick;
use quick\admin\Service;

/**
 * 系统进程管理服务
 * Class ProcessService
 * @package think\admin\service
 */
class ProcessService extends Service
{

    /**
     * 获取 Think 指令内容
     * @param string $args 指定参数
     * @param boolean $simple 指令内容
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function think(string $args = '', bool $simple = false): string
    {
        $command = trim("{$this->app->getRootPath()}think {$args}");
        if ($simple) return $command;
        $binary = sysConfig('base.binary') ?: PHP_BINARY;
        if (in_array(basename($binary), ['php', 'php.exe'])) {
            return "{$binary} {$command}";
        } else {
            return "php {$command}";
        }
    }

    /**
     * 检查 Think 运行进程
     * @param string $args
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function thinkQuery(string $args): array
    {
        return $this->query($this->think($args, true));
    }

    /**
     * 执行 Think 指令内容
     * @param string $args 执行参数
     * @param integer $usleep 延时时间
     * @return ProcessService
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function thinkCreate(string $args, int $usleep = 0): ProcessService
    {
        return $this->create($this->think($args), $usleep);
    }

    /**
     * 创建异步进程
     * @param string $command 任务指令
     * @param integer $usleep 延时时间
     * @return $this
     */
    public function create(string $command, int $usleep = 0): ProcessService
    {
        if ($this->iswin()) {
            $this->exec(__DIR__ . "/bin/console.exe {$command}");
        } else {
            $this->exec("{$command} > /dev/null 2>&1 &");
        }
        if ($usleep > 0) {
            usleep($usleep);
        }
        return $this;
    }

    /**
     * 查询相关进程列表
     * @param string $cmd 任务指令
     * @param string $name 进程名称
     * @return array
     */
    public function query(string $cmd, string $name = 'php.exe'): array
    {
        $list = [];
        if ($this->iswin()) {
            $lines = $this->exec('wmic process where name="' . $name . '" get processid,CommandLine', true);
            foreach ($lines as $line) if ($this->_issub($line, $cmd) !== false) {
                $attr = explode(' ', $this->_space($line));
                $list[] = ['pid' => array_pop($attr), 'cmd' => join(' ', $attr)];
            }
        } else {
            $lines = $this->exec("ps ax|grep -v grep|grep \"{$cmd}\"", true);
            foreach ($lines as $line) if ($this->_issub($line, $cmd) !== false) {
                $attr = explode(' ', $this->_space($line));
                [$pid] = [array_shift($attr), array_shift($attr), array_shift($attr), array_shift($attr)];
                $list[] = ['pid' => $pid, 'cmd' => join(' ', $attr)];
            }
        }
        return $list;
    }

    /**
     * 关闭任务进程
     * @param integer $pid 进程号
     * @return boolean
     */
    public function close(int $pid): bool
    {
        if ($this->iswin()) {
            $this->exec("wmic process {$pid} call terminate");
        } else {
            $this->exec("kill -9 {$pid}");
        }
        return true;
    }

    /**
     * 立即执行指令
     * @param string $command 执行指令
     * @param boolean|array $outarr 返回类型
     * @return string|array
     */
    public function exec(string $command, $outarr = false)
    {
        $descriptor = [
            1 => [
                "pipe", "w"
            ],  // 输出，子进程输出
        ];
        $process = proc_open($command, $descriptor, $pipes);
        $output = fread($pipes[1], 65535);
//        print_r($output);
//        proc_close($process);
        return $outarr ? explode("\n",$output) : $output;
    }

    /**
     * 判断系统类型
     * @return boolean
     */
    public function iswin(): bool
    {
        return PATH_SEPARATOR === ';';
    }

    /**
     * 读取组件版本号
     * @return string
     */
    public function version(): string
    {
        return Quick::version();
    }

    /**
     * 清除空白字符过滤
     * @param string $content
     * @return string
     */
    private function _space(string $content): string
    {
        return preg_replace('|\s+|', ' ', strtr(trim($content), '\\', '/'));
    }

    /**
     * 判断是否包含字符串
     * @param string $content
     * @param string $substr
     * @return boolean
     */
    private function _issub(string $content, string $substr): bool
    {
        return stripos($this->_space($content), $this->_space($substr)) !== false;
    }
}
