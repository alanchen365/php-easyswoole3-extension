<?php

namespace Es3\AutoLoad;

use App\Constant\AppConst;
use App\Module\Employee\Crontab\UserCrontab;
use AsaEs\RemoteCall\Rpc;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\EasySwoole\Logger;
use Es3\EsUtility;

class Crontab
{
    use Singleton;

    public function autoLoad()
    {
        try {
            $crontabLoads = [];
            $path = EASYSWOOLE_ROOT . '/' . AppConst::ES_DIRECTORY_APP_NAME . '/' . AppConst::ES_DIRECTORY_MODULE_NAME . '/';
            $modules = EsUtility::sancDir($path);

            foreach ($modules as $module) {

                $crontabPath = $path . $module . '/' . AppConst::ES_DIRECTORY_CRONTAB_NAME . '/';
                $crontabFiles = EsUtility::sancDir($crontabPath);

                foreach ($crontabFiles as $key => $crontabFile) {

                    $autoLooadFile = $crontabPath . $crontabFile;
                    if (!file_exists($autoLooadFile)) {
                        continue;
                    }

                    /** 获取类名 */
                    $className = basename($autoLooadFile, '.php');

                    /** 加载定时任务 */
                    $class = "\\" . AppConst::ES_DIRECTORY_APP_NAME . "\\" . AppConst::ES_DIRECTORY_MODULE_NAME . "\\" . $module . "\\" . AppConst::ES_DIRECTORY_CRONTAB_NAME . "\\" . $className;

                    if (class_exists($class)) {
                        \EasySwoole\EasySwoole\Crontab\Crontab::getInstance()->addTask($class);
                        echo Utility::displayItem('Crontab', $class);
                        echo "\n";
                    }
                }
            }

        } catch (\Throwable $throwable) {
            echo 'Crontab Initialize Fail :' . $throwable->getMessage();
        }
    }
}
