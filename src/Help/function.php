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