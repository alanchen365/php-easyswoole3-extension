<?php

namespace Es3\Exception;


use EasySwoole\EasySwoole\Logger;

class InfoException extends EsConst
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        /** 录入日志 */
//        Logger::getInstance()->info(json_encode(['code' => $code, 'msg' => $msg]), 'exception');
        parent::__construct($code, $msg, $previous);
    }
}