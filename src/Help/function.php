<?php

use App\Constant\AppConst;
use EasySwoole\Component\Di;
use EasySwoole\Http\Request;
use Es3\Trace;

function isProduction(): bool
{
    return env() === strtolower('PRODUCTION') ? true : false;
}

function isDev(): bool
{
    return env() === strtolower('PRODUCTION') ? false : true;
}

function config($keyPath = '', $env = false)
{
    // 获取当前开发环境
    if ($env) {
        $keyPath = $keyPath . "." . env();
    }
    return EasySwoole\EasySwoole\Config::getInstance()->getConf($keyPath);
}

function isHttp()
{
    $workId = \EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer()->worker_id;

    if ($workId < 0) {
        return false;
    }

    return true;
}

/**
 * 获取当前环境
 * @return string
 */
function env(): string
{
    return strtolower(config('ENV'));
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

function clientIp(): ?string
{
    $request = Di::getInstance()->get(AppConst::DI_REQUEST);
    if (!($request instanceof Request)) {
        return null;
    }

    $ip = \EasySwoole\EasySwoole\ServerManager::getInstance()->getSwooleServer()->connection_info($request->getSwooleRequest()->fd)['remote_ip'] ?? null;
    return $ip;

    //header 地址，例如经过nginx proxy后
    //    $ip2 = $request->getHeaders();
    //    var_dump($ip2);
}

function requestLog(): ?array
{
    if (isHttp()) {
        $request = Di::getInstance()->get(AppConst::DI_REQUEST);

        $swooleRequest = (array)$request->getSwooleRequest();
        $raw = $request->getBody()->__toString();

        $headerServer1 = array_merge($swooleRequest['header'] ?? [], $swooleRequest['server'] ?? []);
        $headerServer2 = [
            'fd' => $swooleRequest['fd'] ?? null,
            'request' => $swooleRequest['request'] ?? null,
            'cookie' => $swooleRequest['cookie'] ?? null,
            'get_params' => $swooleRequest['get'] ?? null,
            'post_params' => $swooleRequest['post'] ?? null,
            'raw' => $raw,
            'files_params' => $swooleRequest['files'] ?? null,
            'tmpfiles' => $swooleRequest['tmpfiles'] ?? null,
        ];
        $headerServer = array_merge($headerServer1, $headerServer2);
        $headerServer['trace_code'] = Trace::getRequestId();

        return $headerServer;
    }

    return null;
}

function setIdentity($identity): void
{
    Di::getInstance()->set(AppConst::HEADER_AUTH, $identity);
}

function identity()
{
    return Di::getInstance()->get(AppConst::HEADER_AUTH);
}

function redisKey(string $key): string
{
    return strtolower(\App\Constant\EnvConst::SERVICE_NAME) . '_' . $key;
}