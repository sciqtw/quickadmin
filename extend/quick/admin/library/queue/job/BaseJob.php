<?php

namespace quick\admin\library\queue\job;

use think\App;
use think\facade\Log;
use think\queue\Job;

class BaseJob
{

    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * @var
     */
    protected $job;


    /**
     * @var
     */
    protected $data;

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        $this->fire(...$arguments);
    }

    /**
     * @param Job $job
     * @param $data
     */
    public function fire(Job $job, $data)
    {

        $this->job = $job;
        $this->data = $data;

        try {
            $action = $data['do'] ?? 'handle';//任务名
            $jobData = $data['data'] ?? [];//执行数据
            $attempts_max = $data['attempts_max'] ?? 0;//最大错误次数
            $rscript = $data['rscript'] ?? 0;//执行任务 0：单例 1：多列
            $release = $data['release'] ?? 20;// 任务重新执行间隔时间秒

            $res = $this->{$action}($job,$jobData);
//            throw new \Exception('dddd');
            if (!$job->isDeletedOrReleased()) {
                // 多列
                if ($rscript) {
                    // 循环任务 重启
                    $job->release($release);
                } else {
                    $job->delete();
                }

            }
            // 超过重发次数删除任务
            if ($attempts_max && $job->attempts() >= $attempts_max ) {
                //删除任务
                $job->delete();
            }

        } catch (\Exception | \Throwable  $e) {
            Log::error('执行消息队出现错误,错误原因:' . $e->getMessage().$e->getLine().$e->getFile());
            echo '执行消息队出现错误,错误原因:' . $e->getMessage();
            $job->failed($e);


        }
    }


    /**
     * 任务失败执行方法
     *
     * @param array $data
     * @param $e
     */
    public function failed(array $data, $e)
    {

    }

    /**
     * @param $job
     * @param array $data
     * @return bool
     */
    public function handle($job, array $data)
    {
        return true;
    }

}