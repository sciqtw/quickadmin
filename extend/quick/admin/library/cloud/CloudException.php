<?php

namespace quick\admin\library\cloud;

class CloudException extends \Exception
{
    public $raw;

    public function __construct($message = '', $code = 0, $previous = null, $raw)
    {
        $this->raw = $raw;
        parent::__construct($message, $code, $previous);
    }
}