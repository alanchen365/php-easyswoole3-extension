<?php

namespace Es3\Exception;


use EasySwoole\EasySwoole\Logger;

class NoticeException extends BaseException
{
    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        /** 录入日志 */
        $data = ['code' => $code, 'msg' => $msg];
        if (isHttp()) {
            $data['request'] = requestLog();
        }

        Logger::getInstance()->notice(jsonEncode($data), 'exception');
        parent::__construct($code, $msg, $previous);
    }
}