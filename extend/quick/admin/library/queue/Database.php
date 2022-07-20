<?php



namespace quick\admin\library\queue;

use stdClass;
use think\Db;
use think\db\ConnectionInterface;
use think\queue\InteractsWithTime;

class Database extends \think\queue\connector\Database
{

    use InteractsWithTime;

    protected $db;

    /**
     * The database table that holds the jobs.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the default queue.
     *
     * @var string
     */
    protected $default;

    /**
     * @var string 执行结果
     */
    protected $desc;

    /**
     * The expiration time of a job.
     *
     * @var int|null
     */
    protected $retryAfter = 60;

    public function __construct(ConnectionInterface $db, $table, $default = 'default', $retryAfter = 60)
    {
        $this->db = $db;
        $this->table = $table;
        $this->default = $default;
        $this->retryAfter = $retryAfter;
    }

    public static function __make(Db $db, $config)
    {
        $connection = $db->connect($config['connection'] ?? null);

        return new self($connection, $config['table'], $config['queue'], $config['retry_after'] ?? 60);
    }

    public function size($queue = null)
    {
        return $this->db
            ->name($this->table)
            ->where([
                'queue' => $this->getQueue($queue),
                'status' => 1,
            ])
            ->count();
    }

    public function push($job, $data = '', $queue = null)
    {
        return $this->pushToDatabase($queue, $this->createPayload($job, $data));
    }

    public function pushRaw($payload, $queue = null, array $options = [])
    {
        return $this->pushToDatabase($queue, $payload);
    }

    public function later($delay, $job, $data = '', $queue = null)
    {
        return $this->pushToDatabase($queue, $this->createPayload($job, $data), $delay);
    }

    public function bulk($jobs, $data = '', $queue = null)
    {
        $queue = $this->getQueue($queue);

        $availableAt = $this->availableAt();

        return $this->db->name($this->table)->insertAll(collect((array)$jobs)->map(
            function ($job) use ($queue, $data, $availableAt) {
                return [
                    'queue' => $queue,
                    'attempts' => 0,
                    'reserve_time' => null,
                    'available_time' => $availableAt,
                    'create_time' => $this->currentTime(),
                    'payload' => $this->createPayload($job, $data),
                ];
            }
        )->all());
    }

    /**
     * 重新发布任务
     * @param string $queue
     * @param stdClass $job
     * @param int $delay
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    public function release($queue, $job, $delay)
    {

        return $this->db->name($this->table)->where('id', $job->id)->update([
            'queue' => $queue,
            'status' => 1,
            'available_time' => $this->availableAt($delay),
            'outer_time' => microtime(true),
        ]);
    }

    /**
     * Push a raw payload to the database with a given delay.
     *
     * @param \DateTime|int $delay
     * @param string|null $queue
     * @param string $payload
     * @param int $attempts
     * @return mixed
     */
    protected function pushToDatabase($queue, $payload, $delay = 0, $attempts = 0)
    {

        $data = json_decode($payload, true);
        return $this->db->name($this->table)->insertGetId([
            'queue' => $this->getQueue($queue),
            'code' => $data['data']['code'] ?? 0,
            'title' => $data['data']['title'] ?? 'title',
            'command' => $data['job'],
            'attempts' => $attempts,
            'attempts_max' => $data['data']['attempts_max'] ?? 3,
            'rscript' => $data['data']['rscript'] ?? 0,
            'status' => 1,
            'reserve_time' => null,
            'payload' => $payload,
            'available_time' => $this->availableAt($delay),
            'enter_time' => 0,
            'outer_time' => 0,
            'loops_time' => $data['data']['release'] ?? 0,
            'create_time' => $this->currentTime(),
        ]);
    }


    /**
     * @param null $queue
     * @return mixed
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        return $this->db->transaction(function () use ($queue) {

            if ($job = $this->getNextAvailableJob($queue)) {

                $job = $this->markJobAsReserved($job);

                return new DatabaseJob($this->app, $this, $job, $this->connection, $queue);
            }
        });
    }

    /**
     * 获取下个有效任务
     *
     * @param $queue
     * @return object|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    protected function getNextAvailableJob($queue)
    {

        $job = $this->db
            ->name($this->table)
            ->lock(true)
            ->where('queue', $this->getQueue($queue))
            ->where([
                ['status', '=', 1],
                ['available_time', '<=', time()]
            ])
            ->order('id', 'asc')
            ->find();

        return $job ? (object)$job : null;
    }


    /**
     * 标记任务正在执行
     * @param $job
     * @return mixed
     * @throws \think\db\exception\DbException
     */
    protected function markJobAsReserved($job)
    {

        $this->db
            ->name($this->table)
            ->where('id', $job->id)
            ->update([
                'reserve_time' => $job->reserve_time = $this->currentTime(),
                'attempts' => ++$job->attempts,
                'enter_time' => microtime(true),
                'outer_time' => 0,
                'desc' => '',
                'status' => 2,
            ]);

        return $job;
    }

    /**
     * 完成任务
     *
     * @param string $id
     * @return void
     */
    public function deleteReserved($id)
    {
        $resDes =  $this->desc;
        $this->db->transaction(function () use ($id,$resDes) {
            if ($res = $this->db->name($this->table)->lock(true)->find($id)) {
                $this->db->name($this->table)->where('id', $id)->update([
                    'status' => 3,
                    'desc' => $resDes,
                    'outer_time' => microtime(true),
                ]);
            }
        });
    }

    /**
     * 任务失败
     *
     * @param string $id
     * @return void
     */
    public function failed($id, \Exception $e)
    {
        $desc = $e->getMessage();
        $this->db->transaction(function () use ($id, $desc) {
            if ($res = $this->db->name($this->table)->lock(true)->find($id)) {
                $this->db->name($this->table)->where('id', $id)->update([
                    'status' => 4,
                    'desc' => $desc,
                    'outer_time' => microtime(true),
                ]);
            }
        });
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

    protected function getQueue($queue)
    {
        return $queue ?: $this->default;
    }
}
