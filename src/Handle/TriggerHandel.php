<?php
//
//namespace Es3\Handle;
//
//use EasySwoole\EasySwoole\Logger;
//use EasySwoole\Trigger\Location;
//use EasySwoole\Trigger\TriggerInterface;
//use Es3\Output\Json;
//
//class TriggerHandel implements TriggerInterface
//{
//    public function error($msg, int $errorCode = E_USER_ERROR, Location $location = null)
//    {
//        var_dump('errorerror');
//        Logger::getInstance()->console('这是自定义输出的错误:' . $msg);
//        // TODO: Implement error() method.
//    }
//
//    public function throwable(\Throwable $throwable)
//    {
//        $e = new \Exception;
//        var_dump($e->getTraceAsString());
//        Json::success(1, 1);
//        Logger::getInstance()->console('这是自定义输出的异常:' . $throwable->getMessage());
//        // TODO: Implement throwable() method.
//    }
//}