<?php

function isProduction(): bool
{
    $env = \Es3\EsConfig::getInstance()->getConf('ENV');
    return $env === strtolower('PRODUCTION') ? true : false;
}

function isDev(): bool
{
    $env = \Es3\EsConfig::getInstance()->getConf('ENV');
    return $env === strtolower('PRODUCTION') ? false : true;
}

function config($keyPath = '', $env = false): string
{
    return \Es3\EsConfig::getInstance()->getConf($keyPath, $env);
}

/**
 * 获取当前环境
 * @return string
 */
function env(): string
{
    $env = \Es3\EsConfig::getInstance()->getConf('ENV');
    return strtolower($env);
}

/**
 * 保留数组中部分元素
 * @param array $array 原始数组
 * @param array $keys 保留的元素
 */
function array_save(array $array, array $keys = []): array
{
    $nList = [];
    foreach ($array as $item => $value) {
        if (in_array($item, $keys)) {
            $nList[$item] = $array[$item];
        }
    }

    return $nList;
}