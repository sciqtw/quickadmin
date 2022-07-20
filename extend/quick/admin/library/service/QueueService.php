<?php

// +----------------------------------------------------------------------
// | ProcessService 来源于开源项目ThinkAdmin
// | github 代码仓库：https://github.com/zoujingli/ThinkLibrary
// +----------------------------------------------------------------------


declare (strict_types=1);


namespace quick\admin\library\service;

use Error;
use quick\admin\http\model\SystemQueue;
use quick\admin\library\queue\job\HandleQuickJob;
use quick\admin\library\queue\job\QuickJobBase;
use quick\admin\library\queue\Progress;
use quick\admin\library\tools\CodeTools;
use quick\admin\Service;
use quick\admin\Exception;
use think\facade\Log;
use think\facade\Queue as ThinkQueue;

/**
 * 任务基础服务
 * Class QueueService
 * @package think\admin\service
 */
class QueueService extends Service
{
    use Progress;

    /**
     * 当前任务编号
     * @var string
     */
    public $code = '';


    /**
     * 列队
     * @var string
     */
    public $queue = 'quick';

    /**
     * 当前任务标题
     * @var string
     */
    public $title = '';

    /**
     * 当前任务参数
     * @var array
     */
    public $data = [];

    /**
     * 当前任务数据
     * @var array
     */
    public $record = [];


    /**
     * 数据初始化
     *
     * @param  $code
     * @param string $queue
     * @return QueueService
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function initialize($code = '', $queue = ''): Service
    {
        if (!empty($code)) {
            $this->code = $code;
            $this->record = SystemQueue::make()->where(['id' => $this->code])->find();
            if (empty($this->record)) {
                $this->app->log->error("Qeueu initialize failed, Queue {$code} not found.");
                throw new Exception("Qeueu initialize failed, Queue {$code} not found.");
            }
            [$this->code, $this->title] = [$this->record['id'], $this->record['title']];
            $this->data = empty($this->record['payload']) ? []:(json_decode($this->record['payload'], true) ?: []);
        }
        if(!empty($queue)){
            $this->queue = $queue;
        }
        return $this;
    }

    /**
     * 重发异步任务
     * @param integer $wait 等待时间
     * @return $this
     * @throws \quick\admin\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function reset(int $wait = 0): QueueService
    {
        if (empty($this->record)) {
            $this->app->log->error("Qeueu reset failed, Queue {$this->code} data cannot be empty!");
            throw new Exception("Qeueu reset failed, Queue {$this->code} data cannot be empty!");
        }
        SystemQueue::where('id', $this->code)->update([
            'status' => 1,
            'available_time' => time() + $wait,
            'outer_time' => microtime(true),
        ]);

        return $this->initialize($this->code);
    }


    /**
     * 注册异步处理任务
     * @param string $title 任务名称
     * @param string $command 执行脚本
     * @param integer $later 延时时间
     * @param array $data 任务附加数据
     * @param integer $rscript 任务类型(0单例,1多例)
     * @param integer $loops 循环等待时间
     * @return QueueService
     * @throws Exception
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function register(string $title, $command, int $later = 0, array $data = [], int $rscript = 0, int $loops = 0): QueueService
    {
        $info = [
            'do' => 'handle',
            'attempts_max' => 0,
            'title' => $title,
            'release' => $loops,
            'rscript' => $rscript,
            'code' => CodeTools::uniqidDate(16, 'Q')
        ];

        if (is_object($command) && $command instanceof QuickJobBase) {

            $info['data'] = [
                'commandName' => get_class($command),
                'command' => serialize(clone $command),
            ];
            $command = HandleQuickJob::class;
        } else {
            $info['data'] = $data ?: [];
        }


        $res = $this->queue($command, $info, $later, $this->queue);
        if (!$res) {
            Log::error('添加列队失败：' . json_encode($info));
            throw new \think\Exception('添加列队失败：' . json_encode($info));
        }
        $this->code = $res;
        $this->progress(1, '>>> 任务创建成功 <<<', '0.00');
        return $this->initialize($this->code);
    }


    /**
     * @param $job
     * @param string $data
     * @param int $delay
     * @param null $queue
     * @return mixed
     */
    protected function queue($job, $data = '', $delay = 0, $queue = null)
    {
        if ($delay > 0) {
            return ThinkQueue::later($delay, $job, $data, $queue);
        } else {
            return ThinkQueue::push($job, $data, $queue);
        }
    }


}