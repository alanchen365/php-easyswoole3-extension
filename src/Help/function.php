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