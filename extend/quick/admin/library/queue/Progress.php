<?php
declare (strict_types=1);

namespace quick\admin\library\queue;



use Error;

trait Progress
{


    /**
     * 当前任务编号
     * @var string
     */
    public $code = '';

    public function getCode()
    {
        return $this->code;
    }

    /**
     * 设置任务进度信息
     * @param ?integer $status 任务状态
     * @param ?string $message 进度消息
     * @param ?string $progress 进度数值
     * @param integer $backline 回退信息行
     * @return array
     */
    public function progress(?int $status = null, ?string $message = null, ?string $progress = null, int $backline = 0): array
    {
        $ckey = "queue_{$this->getCode()}_progress";
        if (is_numeric($status) && intval($status) === 3) {
            if (!is_numeric($progress)) $progress = '100.00';
            if (is_null($message)) $message = '>>> 任务已经完成 <<<';
        }
        if (is_numeric($status) && intval($status) === 4) {
            if (!is_numeric($progress)) $progress = '0.00';
            if (is_null($message)) $message = '>>> 任务执行失败 <<<';
        }
        try {
            $data = app()->cache->get($ckey, [
                'code' => $this->code, 'status' => $status, 'message' => $message, 'progress' => $progress, 'history' => [],
            ]);
        } catch (\Exception | Error $exception) {
            return $this->progress($status, $message, $progress, $backline);
        }
        while (--$backline > -1 && count($data['history']) > 0) array_pop($data['history']);
        if (is_numeric($status)) $data['status'] = intval($status);
        if (is_numeric($progress)) $progress = str_pad(sprintf("%.2f", $progress), 6, '0', STR_PAD_LEFT);
        if (is_string($message) && is_null($progress)) {
            $data['message'] = $message;
            $data['history'][] = ['message' => $message, 'progress' => $data['progress'], 'datetime' => date('Y-m-d H:i:s')];
        } elseif (is_null($message) && is_numeric($progress)) {
            $data['progress'] = $progress;
            $data['history'][] = ['message' => $data['message'], 'progress' => $progress, 'datetime' => date('Y-m-d H:i:s')];
        } elseif (is_string($message) && is_numeric($progress)) {
            $data['message'] = $message;
            $data['progress'] = $progress;
            $data['history'][] = ['message' => $message, 'progress' => $progress, 'datetime' => date('Y-m-d H:i:s')];
        }
        if (is_string($message) || is_numeric($progress)) {
            if (count($data['history']) > 10) {
                $data['history'] = array_slice($data['history'], -10);
            }
            app()->cache->set($ckey, $data, 86400);
        }
        return $data;
    }

    /**
     * 更新任务进度
     * @param integer $total 记录总和
     * @param integer $count 当前记录
     * @param string $message 文字描述
     * @param integer $backline 回退行数
     */
    public function message(int $total, int $count, string $message = '', int $backline = 0): void
    {
        $total = $total < 1 ? 1 : $total;
        $prefix = str_pad("{$count}", strlen("{$total}"), '0', STR_PAD_LEFT);
        $message = "[{$prefix}/{$total}] {$message}";
        $this->progress(2, $message, sprintf("%.2f", $count / $total * 100), $backline);
    }


}
