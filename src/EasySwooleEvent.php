<?php

namespace Es3;


use App\Constant\AppConst;
use App\Constant\EnvConst;
use App\LogPusher;
use EasySwoole\Component\Di;
use EasySwoole\Console\Console;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\EasySwoole\Config;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\SysConst;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\Pool\Exception\Exception;
use EasySwoole\Template\Render;
use Es3\Auth\Policy;
use Es3\Exception\ErrorException;
use Es3\Handle\HttpThrowable;
use Es3\Output\Result;
use Es3\Template\Smarty;
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

        /** 事件注册 */
        \Es3\AutoLoad\Event::getInstance()->autoLoad();

        /** 文档生成 */
//        Es3Doc::getInstance()->generator();

        /** 目录不存在就创建 */
        is_dir(EnvConst::PATH_LOG) ? null : mkdir(EnvConst::PATH_LOG, 0777, true);
        is_dir(EnvConst::PATH_TEMP) ? null : mkdir(EnvConst::PATH_TEMP, 0777, true);
        is_dir(EnvConst::PATH_LOCK) ? null : mkdir(EnvConst::PATH_LOCK, 0777, true);

        /** ORM 注入 */
//        $mysqlConf = EsConfig::getInstance()->getConf('mysql', true);
//        $config = new \EasySwoole\ORM\Db\Config($mysqlConf);
//        DbManager::getInstance()->addConnection(new Connection($config));
    }

    public static function frameInitialize(): void
    {

    }

    public static function mainServerCreate(EventRegister $register): void
    {
        /** 初始化定时任务 */
        \Es3\AutoLoad\Crontab::getInstance()->autoLoad();

        /** 初始化自定义进程 */
        \Es3\AutoLoad\Process::getInstance()->autoLoad();

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

            foreach ($console->allFd() as $item) {
                $console->send($item['fd'], $msg);
            }
//
//            if (Config::getInstance()->getConf('LOG_DIR')) {
//                /*
//                 * 可以在 LogPusher 模型的exec方法中，对loglevel，category进行设置，从而实现对日志等级，和分类的过滤推送
//                 */
//            }
        });

        /** 策略加载 */
        Di::getInstance()->set(AppConst::DI_POLICY, Policy::getInstance()->initialize());

        /** smarty */
        Render::getInstance()->getConfig()->setRender(new Smarty());
        Render::getInstance()->getConfig()->setTempDir(EASYSWOOLE_TEMP_DIR);
        Render::getInstance()->attachServer(ServerManager::getInstance()->getSwooleServer());

        if (isDev()) {
            $hotReloadOptions = new \EasySwoole\HotReload\HotReloadOptions;
            $hotReload = new \EasySwoole\HotReload\HotReload($hotReloadOptions);
            $hotReloadOptions->setMonitorFolder([EASYSWOOLE_ROOT . '/App']);

            $server = ServerManager::getInstance()->getSwooleServer();
            $hotReload->attachToServer($server);
        }

        /** 连接mysql */
        $mysqlConf = EsConfig::getInstance()->getConf('mysql', true);
        if (!superEmpty($mysqlConf)) {

            echo Utility::displayItem('MysqlConf', json_encode($mysqlConf));
            echo "\n";

            $mysqlConfig = new \EasySwoole\ORM\Db\Config($mysqlConf);
            $connection = new Connection($mysqlConfig);
            DbManager::getInstance()->addConnection($connection);
            DbManager::getInstance()->onQuery(function ($res, $builder, $start) {

                $nowDate = date('Y-m-d H:i:s', time());

                /** 打印日志 */
                echo "\n====================  {$nowDate} ====================\n";
                echo $builder->getLastQuery() . "\n";
                echo "==================== {$nowDate} ====================\n";
            });

            $register->add($register::onWorkerStart, function () {
                //链接预热
                DbManager::getInstance()->getConnection()->getClientPool()->keepMin();
            });
        }

        /** 连接redis */
        $redisConf = EsConfig::getInstance()->getConf('redis', true);
        if (superEmpty(!$redisConf)) {
            try {
                echo Utility::displayItem('RedisConf', json_encode($redisConf));
                echo "\n";

                $redisConf = new \EasySwoole\Redis\Config\RedisConfig($redisConf);

                \EasySwoole\RedisPool\Redis::getInstance()->register(EnvConst::REDIS_KEY, $redisConf);
            } catch (Exception $e) {
                throw new ErrorException(1002, 'redis连接失败');
            }
        }
    }

    public static function onRequest(Request $request, Response $response)
    {
        Di::getInstance()->set(AppConst::DI_RESULT, Result::class);
        Di::getInstance()->set(AppConst::DI_REQUEST, $request);
        Di::getInstance()->set(AppConst::DI_RESPONSE, $response);

        /** 请求唯一标识  */
        Trace::createRequestId();

        Middleware::run($request, $response);
    }
}
