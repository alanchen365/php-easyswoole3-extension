<?php

namespace Es3\Exception;

use EasySwoole\EasySwoole\Logger;

class ErrorException extends EsConst
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        /** 录入日志 */
        $data = ['code' => $code, 'msg' => $msg];
        if (isHttp()) {
            $data['request'] = requestLog();
        }
        
        Logger::getInstance()->error(json_encode($data), 'exception');
        parent::__construct($code, $msg, $previous);
    }
}