<?php

namespace Es3;

use App\Constant\AppConst;
use AsaEs\AsaEsConst;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Logger;
use EasySwoole\Http\Request;
use \EasySwoole\Http\Response;

use EasySwoole\Log\LoggerInterface;
use Es3\Handle\LoggerHandel;
use Es3\Middleware\CrossDomain;

class Middleware
{
    public static function onRequest(Request $request, Response $response)
    {
        $request->withAttribute('access_log', microtime(true));

        $self = new self();
        /** 跨域注入 */
        $self->CrossDomain($request, $response);

        /** 空参数过滤 */
        $self->clearEmptyParams($request, $response);

        /** 写入请求日主 */
        $self->access($request, $response);
    }

    public static function afterRequest(Request $request, Response $response)
    {
        $self = new self();

        /**slog */
        $self->slog($request, $response);
    }

    private function crossDomain(Request $request, $response)
    {
        // 任何环境都不做限制
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, system_id, app_code, token, identity');

        /** 生产情况的跨域 由 运维处理 */
        if (!isProduction()) {

            $origin = current($request->getHeader('origin') ?? null) ?? '';
            $origin = rtrim($origin, '/');

            $response->withHeader('Access-Control-Allow-Origin', $origin);
            $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PUT, OPTIONS');
            $response->withHeader('Access-Control-Allow-Credentials', 'true');

            if ($request->getMethod() === 'OPTIONS') {
                $response->withStatus(Status::CODE_OK);
                $response->end();
                return false;
            }
        }
    }

    private function clearEmptyParams(Request $request, $response): void
    {
        if ($request->getMethod() != 'GET') {
            return;
        }

        $params = $request->getQueryParams() ?? [];
        foreach ($params as $key => $val) {
            if (superEmpty($val)) {
                unset($params[$key]);
            }
        }

        $request->withQueryParams($params);
    }

    private function slog(Request $request, $response)
    {
        /** 从请求里获取之前增加的时间戳 */
        $reqTime = $request->getAttribute('access_log');

        /** 计算一下运行时间  */
        $runTime = round(microtime(true) - $reqTime, 5);

        /** 拼接一个简单的日志 */
        $runTime = round(floatval($runTime * 1000), 0);
        $accessLog = $request->getUri() . ' | ' . clientIp() . ' | ' . $runTime . ' ms | ' . $request->getHeader('user-agent')[0];

        /** 正常日志 */
        $log = Logger::getInstance()->log($accessLog, LoggerInterface::LOG_LEVEL_INFO, 'access_log');

        /** 慢日志 */
        if ($runTime > round(AppConst::LOG_SLOG_SECONDS * 1000, 0)) {
            $log = Logger::getInstance()->log($accessLog, LoggerInterface::LOG_LEVEL_WARNING, 'slog');
        }
    }

    private function access(Request $request, $response)
    {
        $accessLog = $request->getUri() . ' | ' . clientIp() . ' | ' . $request->getHeader('user-agent')[0];
        Logger::getInstance()->log($accessLog, LoggerInterface::LOG_LEVEL_INFO, 'access_log');
    }
}
