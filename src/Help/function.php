<?php

function isProduction()
{
    $env = \Es3\EsConfig::getInstance()->getConf('ENV');
    return $env === strtolower('PRODUCTION') ? true : false;
}

function isDev()
{
    $env = \Es3\EsConfig::getInstance()->getConf('ENV');
    return $env === strtolower('PRODUCTION') ? false : true;
}