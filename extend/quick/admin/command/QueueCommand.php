<?php

// +----------------------------------------------------------------------
// | queue基于ThinkAdmin修改
// | github 代码仓库：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------

declare (strict_types=1);

namespace quick\admin\command;

use quick\admin\library\queue\Listener;
use quick\admin\library\queue\Worker;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\queue\event\JobFailed;
use think\queue\event\JobProcessed;
use think\queue\event\JobProcessing;
use think\queue\Job;

/**
 * 异步任务管理指令
 * Class Queue
 * @package think\admin\command
 */

class QueueCommand extends Command
{

    /**
     * 任务进程
     */
    const QUEUE_LISTEN = 'quick:queue listen';

    /** @var  Listener */
    protected $listener;

    /** @var  Worker */
    protected $worker;

    public function __construct(Listener $listener,Worker $worker)
    {
        parent::__construct();
        $this->listener = $listener;

        $this->listener->setOutputHandler(function ($type, $line) {
            $this->output->write($line);
        });

        $this->worker = $worker;
    }

    protected function configure()
    {

        $this->setName('quick:queue')
            ->addOption('daemon', 'd', Option::VALUE_NONE, 'The queue listen in daemon mode')
            ->addArgument('action', Argument::OPTIONAL, 'stop|start|status|query|listen|clean|dorun|webstop|webstart|webstatus', 'listen')
            ->addOption('queue', null, Option::VALUE_OPTIONAL, 'The queue to listen on', null)
            ->addOption('delay', null, Option::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0)
            ->addOption('memory', null, Option::VALUE_OPTIONAL, 'The memory limit in megabytes', 128)
            ->addOption('timeout', null, Option::VALUE_OPTIONAL, 'Seconds a job may run before timing out', 60)
            ->addOption('sleep', null, Option::VALUE_OPTIONAL, 'Seconds to wait before checking queue for jobs', 3)
            ->addOption('tries', null, Option::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0)
            ->setDescription('Listen to a given queue');

//        $this->setName('queue:work')
//            ->addArgument('connection', Argument::OPTIONAL, 'The name of the queue connection to work', null)
//            ->addOption('queue', null, Option::VALUE_OPTIONAL, 'The queue to listen on')
//            ->addOption('once', null, Option::VALUE_NONE, 'Only process the next job on the queue')
//            ->addOption('delay', null, Option::VALUE_OPTIONAL, 'Amount of time to delay failed jobs', 0)
//            ->addOption('force', null, Option::VALUE_NONE, 'Force the worker to run even in maintenance mode')
//            ->addOption('memory', null, Option::VALUE_OPTIONAL, 'The memory limit in megabytes', 128)
//            ->addOption('timeout', null, Option::VALUE_OPTIONAL, 'The number of seconds a child process can run', 60)
//            ->addOption('sleep', null, Option::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3)
//            ->addOption('tries', null, Option::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 0)
//            ->setDescription('Process the next job on a queue');
    }

    /**
     * 任务执行入口
     * @param Input $input
     * @param Output $output
     * @return void
     */
    protected function execute(Input $input, Output $output)
    {
        $action = $input->hasOption('daemon') ? 'start' : $input->getArgument('action');
        if (method_exists($this, $method = "{$action}Action")) return $this->$method();
        $this->output->error("># Wrong operation, Allow stop|start|status|query|listen|clean|dorun|webstop|webstart|webstatus");
    }


    protected function worker(Input $input, Output $output)
    {
        $connection = $input->getArgument('connection') ?: $this->app->config->get('queue.default');

        $queue = $input->getOption('queue') ?: $this->app->config->get("queue.connections.{$connection}.queue", 'default');
        $delay = $input->getOption('delay');
        $sleep = $input->getOption('sleep');
        $tries = $input->getOption('tries');

        $this->listenForEvents();

        if ($input->getOption('once')) {
            $this->worker->runNextJob($connection, $queue, $delay, $sleep, $tries);
        } else {
            $memory  = $input->getOption('memory');
            $timeout = $input->getOption('timeout');
            $this->worker->daemon($connection, $queue, $delay, $sleep, $tries, $memory, $timeout);
        }
    }

    /**
     * 查询所有任务
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function queryAction()
    {
        $list = $this->process->thinkQuery('quick:queue');
        if (count($list) > 0) foreach ($list as $item) {
            $this->output->writeln("># {$item['pid']}\t{$item['cmd']}");
        } else {
            $this->output->writeln('># No related task process found');
        }
    }

    /**
     * 查询兼听状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function statusAction()
    {
        if (count($result = $this->process->thinkQuery(static::QUEUE_LISTEN)) > 0) {
            $this->output->writeln("Listening for main process {$result[0]['pid']} running");
        } else {
            $this->output->writeln("The Listening main process is not running");
        }
    }



    /**
     * 停止所有任务
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function stopAction()
    {
        if (count($result = $this->process->thinkQuery('quick:queue')) < 1) {
            $this->output->writeln("># There are no task processes to stop");
        } else foreach ($result as $item) {
            $this->process->close(intval($item['pid']));
            $this->output->writeln("># Successfully sent end signal to process {$item['pid']}");
        }
    }


    /**
     * 启动后台任务
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function startAction()
    {

        $this->output->comment(">$ {$this->process->think(static::QUEUE_LISTEN)}");
        if (count($result = $this->process->thinkQuery(static::QUEUE_LISTEN)) > 0) {
            $this->output->writeln("># Queue daemons already exist for pid {$result[0]['pid']}");
        } else {
            $this->process->thinkCreate(static::QUEUE_LISTEN, 1000);
            if (count($result = $this->process->thinkQuery(static::QUEUE_LISTEN)) > 0) {
                $this->output->writeln("># Queue daemons started successfully for pid {$result[0]['pid']}");
            } else {
                $this->output->writeln("># Queue daemons failed to start");
            }
        }
    }


    public function listenAction()
    {
        $connection = $this->app->config->get('queue.default');

        $queue   = $this->input->getOption('queue') ?: $this->app->config->get("queue.connections.{$connection}.queue", 'default');
        $delay   = $this->input->getOption('delay');
        $memory  = $this->input->getOption('memory');
        $timeout = $this->input->getOption('timeout');
        $sleep   = $this->input->getOption('sleep');
        $tries   = $this->input->getOption('tries');


        $this->listener->listen($connection, $queue, $delay, $sleep, $tries, $memory, $timeout);
    }



    /**
     * 注册事件
     */
    protected function listenForEvents()
    {
        $this->app->event->listen(JobProcessing::class, function (JobProcessing $event) {
            $this->writeOutput($event->job, 'starting');
        });

        $this->app->event->listen(JobProcessed::class, function (JobProcessed $event) {
            $this->writeOutput($event->job, 'success');
        });

        $this->app->event->listen(JobFailed::class, function (JobFailed $event) {
            $this->writeOutput($event->job, 'failed');

            $this->logFailedJob($event);
        });
    }

    /**
     * Write the status output for the queue worker.
     *
     * @param Job $job
     * @param     $status
     */
    protected function writeOutput(Job $job, $status)
    {
        switch ($status) {
            case 'starting':
                $this->writeStatus($job, 'Processing', 'comment');
                break;
            case 'success':
                $this->writeStatus($job, 'Processed', 'info');
                break;
            case 'failed':
                $this->writeStatus($job, 'Failed', 'error');
                break;
        }
    }

    /**
     * Format the status output for the queue worker.
     *
     * @param Job    $job
     * @param string $status
     * @param string $type
     * @return void
     */
    protected function writeStatus(Job $job, $status, $type)
    {
        $this->output->writeln(sprintf(
            "<{$type}>[%s][%s] %s</{$type}> %s",
            date('Y-m-d H:i:s'),
            $job->getJobId(),
            str_pad("{$status}:", 11),
            $job->getName()
        ));
    }

    /**
     * 记录失败任务
     * @param JobFailed $event
     */
    protected function logFailedJob(JobFailed $event)
    {
        $this->app['queue.failer']->log(
            $event->connection,
            $event->job->getQueue(),
            $event->job->getRawBody(),
            $event->exception
        );
    }
}
