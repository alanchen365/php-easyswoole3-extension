<?php

namespace Es3;

use App\Constant\AppConst;
use EasySwoole\ORM\Utility\Schema\Table;

class EsUtility
{

    /**
     * 扫描目录
     */
    public static function sancDir(string $path): array
    {
        if (!is_dir($path)) {
            return [];
        }

        $files = scandir($path) ?? [];
        foreach ($files as $key => $dir) {
            // 过滤空
            if (in_array($dir, ['..', '.'])) {
                unset($files[$key]);
            }
        }

        return $files;
    }

    /**
     * 获取控制器中的class
     * @param string $controllerNameSpace
     * @return string
     */
    public static function getControllerClassName(string $controllerNameSpace): string
    {
        $className = explode('\\', $controllerNameSpace);
        $className = (string)end($className);

        return $className;
    }

    public static function getControllerModuleName(string $controllerNameSpace): string
    {
        $className = explode('\\', $controllerNameSpace);
        $ModuleName = array_slice($className, -2, 1);
        return end($ModuleName);
    }

    public static function tableLogicDelete(Table $schemaInfo): ?string
    {
        $columns = $schemaInfo->getColumns();

        foreach (AppConst::TABLE_LOGIC_DELETE as $field) {
            if (isset($columns[$field])) {
                return $field;
            }
        }
        return null;
    }

    public static function clearParams($params = []): ?array
    {
        /** 清理掉不相关变量 */
        foreach ($params as $field => $value) {
            if (!isset($columns[$field])) {
                unset($params[$field]);
            }
        }
        return $params ?? null;
    }

//    public static function getClassNameByFile(string $file): string
//    {
////        $fileName = explode('/', $file);
////        $className = (string)end($fileName);
////        var_dump($className, '$className');
//
//        $className = basename($file, '.php');
//        return $className;
//    }
}
