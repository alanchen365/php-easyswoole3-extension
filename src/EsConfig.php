<?php

namespace Es3;

use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

/**
 * 配置自动加载
 * Class HttpRouter
 * @package Es3\Autoload
 */
class EsConfig extends Config
{
    use Singleton;

    public function getConf($keyPath = '', $env = false)
    {
        // 获取当前开发环境
        if ($env) {
            $keyPath = $keyPath . "." . strtolower($this->getEnv());
        }
        return Config::getInstance()->getConf($keyPath);
    }

    /**
     * 开发环境 本机(local) 开发服务器(dev) 生产服务器(production)
     * @return string
     */
    public function getEnv(): string
    {
        return strtolower($this->getConf('ENV'));
    }

//    /**
//     * 是否是生产环境
//     */
//    public function isProduction(): bool
//    {
//        $env = strtolower($this->getConf('ENV'));
//        return $env === strtolower('PRODUCTION') ? true : false;
//    }
}
