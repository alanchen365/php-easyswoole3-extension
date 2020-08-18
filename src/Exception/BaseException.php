<?php

namespace Es3\Exception;


class BaseException extends \Exception
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        parent::__construct($msg, $code, $this);
    }
}