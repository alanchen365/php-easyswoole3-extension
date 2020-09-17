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

function config($keyPath = '', $env = false)
{
    return \Es3\EsConfig::getInstance()->getConf($keyPath, $env);
}