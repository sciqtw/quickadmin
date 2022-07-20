<?php

namespace quick\admin\library\queue\job;

use think\queue\Job;

/**
 * Interface QuickJobInterface
 * @package plugins\demo\jobs
 */
interface QuickJobInterface
{
    /**
     * @param $job
     * @return mixed
     */
    public function handel($job);
}
