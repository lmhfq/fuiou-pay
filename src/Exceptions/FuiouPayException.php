<?php

namespace Lmh\Fuiou\Exceptions;


use Throwable;

class FuiouPayException extends \Exception
{
    private $errCode;
    public function __construct($message = '', $errCode = '', $code = 0, Throwable $previous = null)
    {
        $this->errCode = $errCode;
        parent::__construct($message, $code, $previous);
    }
}