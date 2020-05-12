<?php

namespace Es3\Autoload;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use Es3\EsConfig;
use FastRoute\RouteCollector;

/**
 * 配置自动加载
 * Class HttpRouter
 * @package Es3\Autoload
 */
class Config
{
    use Singleton;

    /**
     * 自动加载配置文件
     */
    public function autoload()
    {
        $oldConf = \EasySwoole\EasySwoole\Config::getInstance()->toArray();

        $instance = EsConfig::getInstance();
        $instance->load($oldConf);

        $path = EASYSWOOLE_ROOT . '/Conf/';
        $files = scandir($path) ?? [];

        foreach ($files as $file) {

            $routerFile = $path . $file;
            if (!file_exists($routerFile) || ($file == '.' || $file == '..')) {
                continue;
            }

            $data = require_once $routerFile ?? [];
            foreach ($data as $key => $conf) {
                $instance->setConf(strtolower(basename($file, '.php')), (array)$data);
//                $oldConf = $oldConf + $conf;
            }
        }

    }
}
