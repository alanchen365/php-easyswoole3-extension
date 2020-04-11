<?php

namespace Es3\Autoload;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\AbstractInterface\AbstractRouter;
use FastRoute\RouteCollector;

/**
 * 路由注册类
 * Class Router
 * @package App\HttpControllers
 */
class HttpRouter
{
    protected $router = [];

    use Singleton;

    /**
     * 待注入路由配置
     */
    public function registered(): void
    {
        $this->loadConfig();
    }

    /**
     * 路由注册
     */
    public function register(RouteCollector $routeCollector): void
    {
        foreach ($this->router as $file) {
            foreach ($file as $rKey => $rType) {
                foreach ($rType as $perfix => $routerFunction) {
                    $routeCollector->addGroup($rKey . $perfix, $routerFunction);
                }
            }
        }
    }

    /**
     * 写入配置文件
     */
    private function loadConfig(): void
    {

        // 动态获取路由
        $path = EASYSWOOLE_ROOT . "/App/Module";
        $files = scandir($path) ?? [];

        foreach ($files as $key => $dir) {
            //过滤非目录
            if (strpos($dir, '.') !== false) {
                unset($files[$key]);
            }
        }

        // 获取路由文件下所有目录
        foreach ($files as $dir) {
            $routerFile = $path . '/' . $dir . '/router.php';
            if (!file_exists($routerFile)) {
                continue;
            }
            $data = require_once $routerFile;
            $this->router[] = $data;
        }
    }
}
