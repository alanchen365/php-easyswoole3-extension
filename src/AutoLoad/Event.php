<?php

namespace Es3\AutoLoad;

use App\Constant\AppConst;
use App\Module\Employee\Crontab\UserCrontab;
use AsaEs\RemoteCall\Rpc;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\EasySwoole\Logger;
use Es3\EsUtility;
use function foo\func;

class Event
{
    use Singleton;

    public function autoLoad()
    {
        try {
            $crontabLoads = [];
            $path = EASYSWOOLE_ROOT . '/' . AppConst::ES_DIRECTORY_APP_NAME . '/' . AppConst::ES_DIRECTORY_MODULE_NAME . '/';
            $modules = EsUtility::sancDir($path);

            foreach ($modules as $module) {

                \Es3\Event::getInstance()->set($module, function ($module, ...$args) use ($path) {

                    $module = ucwords($module);
                    $eventPath = $path . $module . '/' . AppConst::ES_FILE_NAME_EVENT;
                    if (!file_exists($eventPath)) {
                        Logger::getInstance()->notice("没有找到" . $eventPath . "事件文件");
                        return;
                    }
                    $namespace = "\\" . AppConst::ES_DIRECTORY_APP_NAME . "\\" . AppConst::ES_DIRECTORY_MODULE_NAME . "\\" . $module . "\\" . AppConst::ES_DIRECTORY_EVENT_NAME;
                    if (!class_exists($namespace)) {
                        Logger::getInstance()->notice("没有找到" . $namespace . "事件命名空间");
                        return;
                    }

                    $ref = new \ReflectionClass($namespace);
                    if (!($ref->hasMethod('run') && $ref->getMethod('run')->isPublic() && !$ref->getMethod('run')->isStatic())) {
                        Logger::getInstance()->notice("没有找到" . $namespace . "的run方法");
                        return;
                    }

                    $namespace = new $namespace();
                    $namespace->run($module, ...$args);
                });

                echo Utility::displayItem('Event', strtolower($module));
                echo "\n";
            }

        } catch (\Throwable $throwable) {
            echo 'Event Initialize Fail :' . $throwable->getMessage();
        }
    }
}
