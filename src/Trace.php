<?php

namespace Es3;

use App\Constant\AppConst;
use App\Constant\ResultConst;
use EasySwoole\Component\Di;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

/**
 * 配置自动加载
 * Class HttpRouter
 * @package Es3\Autoload
 */
class Trace
{
    /**
     * @return mixed
     */
    public static function getRequestId()
    {
        if (!Di::getInstance()->get(AppConst::ID_KEY)) {
            Trace::createRequestId();
        }

        return Di::getInstance()->get(AppConst::ID_KEY);
    }

    /**
     * @param mixed $requestId
     */
    public static function createRequestId(): void
    {
        Di::getInstance()->set(AppConst::ID_KEY, md5(uniqid(microtime(true), true)));
    }
}
