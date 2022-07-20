<?php


namespace quick\admin;

/**
 * Class Exception
 * @package quick\admin
 */
class Exception extends \Exception
{
    /**
     * 异常数据对象
     * @var mixed
     */
    protected $data = [];

    /**
     * @var int
     */
    protected $statusCode = 500;

    /**
     * Exception constructor.
     * @param string $message
     * @param int $code
     * @param array $data
     * @param int $statusCode
     */
    public function __construct(string $message = "", int $code = 0, array $data = [], int $statusCode = 500)
    {
        $this->code = $code;
        $this->data = $data;
        $this->message = $message;
        $this->statusCode = $statusCode;
        parent::__construct($message, $code);
    }


    /**
     * @return string
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 获取异常停止数据
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    public function getError()
    {
        return $this->message;
    }


    /**
     * 设置异常停止数据
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}