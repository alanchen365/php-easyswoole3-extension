<?php

namespace Es3;


use App\Constant\AppConst;
use App\Constant\EnvConst;
use App\LogPusher;
use EasySwoole\Component\Di;
use EasySwoole\Console\Console;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\SysConst;
use Es3\Handle\HttpThrowable;
use Es3\ThrowableHandle\Handle;
use Es3Doc\Es3Doc;

class EasySwooleEvent
{
    public static function initialize(): void
    {
        date_default_timezone_set('Asia/Shanghai');

        /** 加载配置文件 */
        \Es3\AutoLoad\Config::getInstance()->autoLoad();

        /** 路由初始化 */
        \Es3\AutoLoad\Router::getInstance()->autoLoad();

        /** 配置控制器命名空间 */
        Di::getInstance()->set(SysConst::HTTP_CONTROLLER_NAMESPACE, 'App\\Controller\\');

        /** 注入http异常处理 */
        Di::getInstance()->set(SysConst::HTTP_EXCEPTION_HANDLER, [HttpThrowable::class, 'run']);

        /** 文档生成 */
        Es3Doc::getInstance()->generator();

        /** 目录不存在就创建 */
        is_dir(EnvConst::PATH_LOG) ? null : mkdir(EnvConst::PATH_LOG, 0777);
        is_dir(EnvConst::PATH_TEMP) ? null : mkdir(EnvConst::PATH_TEMP, 0777);
    }

    public static function frameInitialize(): void
    {

    }

    public static function mainServerCreate(): void
    {
        $consoleName = EnvConst::SERVICE_NAME . '.console';
        ServerManager::getInstance()->addServer($consoleName, EnvConst::CONSOLE_PORT, SWOOLE_TCP, AppConst::SERVER_HOST, [
            'open_eof_check' => false
        ]);

        $consoleTcp = ServerManager::getInstance()->getSwooleServer($consoleName);
        $console = new Console($consoleName, EnvConst::CONSOLE_AUTH);

        /*
    * 注册日志模块
    */
        $console->moduleContainer()->set(new LogPusher());
        $console->protocolSet($consoleTcp)->attachToServer(ServerManager::getInstance()->getSwooleServer());
        /*
         * 给es的日志推送加上hook
         */
        Logger::getInstance()->onLog()->set('remotePush', function ($msg, $logLevel, $category) use ($console) {

            var_dump('sss');
            foreach ($console->allFd() as $item) {
                $console->send($item['fd'], $msg);
            }
//
//            if (Config::getInstance()->getConf('LOG_DIR')) {
//                /*
//                 * 可以在 LogPusher 模型的exec方法中，对loglevel，category进行设置，从而实现对日志等级，和分类的过滤推送
//                 */
//
//            }
        });
    }
}
