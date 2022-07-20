<?php

namespace quick\admin\library\queue\job;


class HandleQuickJob extends BaseJob
{


    /**
     * @param $job
     * @param array $data
     * @return bool|mixed
     */
    public function handle($job, array $data)
    {

        $command = unserialize($data['command']);
        return $this->app->invoke([$command, 'handle'],[$job]);
    }


    /**
     * @param array $data
     * @param $e
     */
    public function failed(array $data,$e)
    {
        if(!empty($data['data']['command'])){
            $command = unserialize($data['data']['command']);

            if (method_exists($command, 'failed')) {
                $command->failed();
            }
        }

    }

}