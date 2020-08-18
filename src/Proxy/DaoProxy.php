<?php

namespace Es3\Proxy;


use App\Constant\AppConst;
use EasySwoole\EasySwoole\Logger;
use Es3\EsUtility;

class DaoProxy
{
    protected $dao;

    function __construct($namespace)
    {
        $className = EsUtility::getControllerClassName($namespace);
        $moduleName = EsUtility::getControllerModuleName($namespace);

        $moduleDirName = AppConst::ES_DIRECTORY_MODULE_NAME;
        $namespace = "App\\{$moduleDirName}\\{$moduleName}\\Dao\\{$className}Dao";

        if (class_exists($namespace)) {
            $this->dao = new $namespace();
        }else {
            $msg = 'dao 加载失败 : ' . $namespace;
            Logger::getInstance()->console($msg, 3, 'proxy');
        }
    }

    /**
     * @return mixed
     */
    public function getDao()
    {
        return $this->dao;
    }
}
