<?php

namespace Es3\Logger;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use EasySwoole\Log\LoggerInterface;
use EasySwoole\Utility\Time;
use Es3\Trace;
use FastRoute\RouteCollector;

class EsLogger implements LoggerInterface
{
    use Singleton;

    public function log(?string $msg, int $logLevel = EsLogger::LOG_LEVEL_INFO, string $category = 'debug'): string
    {
        

//        return Logger::getInstance()->log(json_encode($esLogBean->toArray($key)), $logLevel, $category);
    }

    public function console(?string $msg, int $logLevel = EsLogger::LOG_LEVEL_INFO, string $category = 'debug')
    {
        return Logger::getInstance()->console(json_encode($esLogBean->toArray(['log_level'])), $logLevel, $category);
    }

    public function info(?string $msg, string $category = 'debug')
    {
        $this->console($msg, EsLogger::LOG_LEVEL_INFO, $category);
    }

    public function notice(?string $msg, string $category = 'debug')
    {
        $this->console($msg, EsLogger::LOG_LEVEL_NOTICE, $category);
    }

    public function waring(?string $msg, string $category = 'debug')
    {
        $this->console($msg, EsLogger::LOG_LEVEL_WARNING, $category);
    }

    public function error(?string $msg, string $category = 'debug')
    {
        $this->console($msg, EsLogger::LOG_LEVEL_ERROR, $category);
    }

    public function onLog(): Event
    {
        $requestId = Trace::getRequestId();
        $this->console($msg, self::LOG_LEVEL_INFO, $category);
    }

    private function levelMap(int $level)
    {
        switch ($level) {
            case self::LOG_LEVEL_INFO:
                return 'info';
            case self::LOG_LEVEL_NOTICE:
                return 'notice';
            case self::LOG_LEVEL_WARNING:
                return 'warning';
            case self::LOG_LEVEL_ERROR:
                return 'error';
            default:
                return 'unknown';
        }
    }
}
