<?php

namespace quick\admin\library\queue;


use think\App;
use think\queue\Job;

class DatabaseJob extends Job
{
    use Progress;

    /**
     * The database queue instance.
     * @var Database
     */
    protected $database;

    /**
     * The database job payload.
     * @var Object
     */
    protected $job;

    protected $desc;

    public function __construct(App $app, Database $database, $job, $connection, $queue)
    {
        $this->app = $app;
        $this->job = $job;
        $this->queue = $queue;
        $this->database = $database;
        $this->connection = $connection;

        $this->progress(2,'>>> 任务开始执行 <<<','0');

    }

    /**
     * 任务执行完毕
     *
     */
    public function delete()
    {
        parent::delete();
        if(!empty($this->desc)){
            $this->database->setDesc($this->desc);
        }

        $this->database->deleteReserved($this->job->id);

        $this->progress(3,'>>> 任务处理完成 <<<', '100.00');
    }


    /**
     * Process an exception that caused the job to fail.
     *
     * @param \Exception $e
     * @return void
     */
    public function failed($e)
    {
        $this->database->failed($this->job->id,$e);
        $this->progress(4,'>>> 任务处理失败 <<<');
        parent::failed($e);

    }


    /**
     * 重新发布任务
     *
     * @param int $delay
     * @throws \think\db\exception\DbException
     */
    public function release($delay = 0)
    {
        parent::release($delay);

        $this->database->release($this->queue, $this->job, $delay);
        $this->progress(3,">>> 第 {$this->attempts()} 次执行 任务处理完成 ({$delay}秒后重新执行) <<<", '100.00');
    }

    /**
     * 获取当前任务尝试次数
     * @return int
     */
    public function attempts()
    {
        return (int)$this->job->attempts;
    }

    /**
     * Get the raw body string for the job.
     * @return string
     */
    public function getRawBody()
    {
        return $this->job->payload;
    }

    /**
     * Get the job identifier.
     *
     * @return string
     */
    public function getJobId()
    {
        return $this->job->id;
    }


    public function getCode()
    {
        return $this->job->id;
    }

    /**
     * 设置执行结果
     * @param string $value
     * @return $this
     */
    public function setDesc(string $value)
    {
        $this->desc = $value;
        return $this;
    }

}
