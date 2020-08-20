<?php

namespace Es3\Proxy;


use App\Constant\AppConst;
use EasySwoole\EasySwoole\Logger;
use Es3\EsUtility;

class ModelProxy
{
    protected $model;

    function __construct($namespace)
    {
        $className = EsUtility::getControllerClassName($namespace);
        $moduleName = EsUtility::getControllerModuleName($namespace);

        $moduleDirName = AppConst::ES_DIRECTORY_MODULE_NAME;
        $namespace = "App\\{$moduleDirName}\\{$moduleName}\\Model\\{$className}Model";

        if ($moduleName == AppConst::ES_DIRECTORY_CONTROLLER_NAME) {
            return;
        }

        if (class_exists($namespace)) {
            $this->model = new $namespace();
        } else {
            $msg = 'model 加载失败 : ' . $namespace;
            Logger::getInstance()->console($msg, 3, 'proxy');
        }
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }
}
