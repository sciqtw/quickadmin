<?php


namespace app\admin\quick\actions;


use quick\admin\library\queue\job\BaseJob;

class UpgradeJob extends BaseJob
{


    /**
     * @param $queue
     * @throws \Exception
     */
    public function handle($job,$data)
    {

        $total = 10;
        $num = 1;
       while ($num < $total){
           $num++;
           $job->progress(2,'>>>进行到地方的反馈'.$num, sprintf("%.2f", $num / $total * 100));
           sleep(1);
       }
       $job->setDesc('执行超过');
//        throw new \Exception('dfd');

    }


}
