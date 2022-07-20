<?php


declare (strict_types=1);

namespace quick\admin\command;


use quick\admin\library\service\ProcessService;
use quick\admin\library\service\QueueService;
use think\console\Input;
use think\console\Output;

/**
 * 自定义指令基类
 * Class Command
 * @package quick\admin
 */
abstract class Command extends \think\console\Command
{
    /**
     * 任务控制服务
     * @var QueueService
     */
    protected $queue;

    /**
     * 进程控制服务
     * @var ProcessService
     */
    protected $process;

    /**
     * 初始化指令变量
     * @param Input $input
     * @param Output $output
     * @return static
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function initialize(Input $input, Output $output): Command
    {
        $this->queue = QueueService::instance();
        $this->process = ProcessService::instance();
        if (defined('WorkQueueCode')) {
            if (!$this->queue instanceof QueueService) {
                $this->queue = QueueService::instance();
            }
            if ($this->queue->code !== WorkQueueCode) {
                $this->queue->initialize(WorkQueueCode);
            }
        }
        return $this;
    }

    /**
     * 设置失败消息并结束进程
     *
     * @param string $message
     * @return Command
     * @throws \quick\admin\Exception
     */
    protected function setQueueError(string $message): Command
    {
        if (defined('WorkQueueCode')) {
            $this->queue->error($message);
        } else {
            $this->output->writeln($message);
            exit("\r\n");
        }
        return $this;
    }

    /**
     * 设置成功消息并结束进程
     *
     * @param string $message
     * @return Command
     * @throws \quick\admin\Exception
     */
    protected function setQueueSuccess(string $message): Command
    {
        if (defined('WorkQueueCode')) {
            $this->queue->success($message);
        } else {
            $this->output->writeln($message);
            exit("\r\n");
        }
        return $this;
    }

    /**
     * 设置进度消息并继续执行
     * @param null|string $message 进度消息
     * @param null|string $progress 进度数值
     * @param integer $backline 回退行数
     * @return static
     */
    protected function setQueueProgress(?string $message = null, ?string $progress = null, int $backline = 0): Command
    {
        if (defined('WorkQueueCode')) {
            $this->queue->progress(2, $message, $progress, $backline);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

    /**
     * 更新任务进度
     * @param integer $total 记录总和
     * @param integer $count 当前记录
     * @param string $message 文字描述
     * @param integer $backline 回退行数
     * @return static
     */
    public function setQueueMessage(int $total, int $count, string $message = '', int $backline = 0): Command
    {
        $total = $total < 1 ? 1 : $total;
        $prefix = str_pad("{$count}", strlen("{$total}"), '0', STR_PAD_LEFT);
        return $this->setQueueProgress("[{$prefix}/{$total}] {$message}", sprintf("%.2f", $count / $total * 100), $backline);
    }
}