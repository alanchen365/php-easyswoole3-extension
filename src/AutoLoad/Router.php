<?php

namespace Es3\AutoLoad;

use App\Constant\AppConst;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use Es3\EsConst;
use FastRoute\RouteCollector;

/**
 * 路由注册类
 * Class Router
 * @package App\HttpControllers
 */
class Router
{
    protected $router = [];

    use Singleton;

    /**
     * 待注入路由配置
     */
    public function autoLoad(): void
    {
        try {
            $path = EASYSWOOLE_ROOT . '/' . AppConst::ES_DIRECTORY_APP_NAME . '/' . AppConst::ES_DIRECTORY_MODULE_NAME . '/';
//            $path = EASYSWOOLE_ROOT . "/App/Module";
            $files = scandir($path) ?? [];
            
            foreach ($files as $key => $dir) {
                //过滤非目录
                if (strpos($dir, '.') !== false) {
                    unset($files[$key]);
                }
            }

            // 获取路由文件下所有目录
            foreach ($files as $dir) {
                $routerFile = $path . $dir . '/' . AppConst::ES_FILE_NAME_ROUTER;
                if (!file_exists($routerFile)) {
                    continue;
                }
                $data = require_once $routerFile;
                echo  Utility::displayItem('Router',$routerFile);

                $this->router[] = $data;
            }

            echo "\n";
        } catch (\Throwable $throwable) {
            echo 'Router Initialize Fail :' . $throwable->getMessage();
        }
    }

    /**
     * 路由注册
     */
    public function initialize(RouteCollector $routeCollector): void
    {
        foreach ($this->router as $file) {
            foreach ($file as $rKey => $rType) {
                foreach ($rType as $perfix => $routerFunction) {
                    $routeCollector->addGroup($rKey . $perfix, $routerFunction);
                }
            }
        }
    }
}
