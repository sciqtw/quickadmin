<?php
namespace quick\admin\library\queue\job;

use think\facade\Log;
use think\queue\Job;

class QuickJobBase implements QuickJobInterface
{

    /**
     * @var Job
     */
    public $job;


    /**
     * @param Job $job
     * @return $this
     */
    public function setJob(Job $job)
    {
        $this->job = $job;
        return $this;
    }


    /**
     * @param $job
     * @return mixed|void
     */
   public function handel($job)
   {
       // TODO: Implement execute() method.
   }




}