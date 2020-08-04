<?php

namespace Es3;

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
