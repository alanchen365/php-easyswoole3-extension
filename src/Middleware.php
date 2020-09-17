<?php

namespace Es3;

use EasySwoole\Http\Request;
use \EasySwoole\Http\Response;

use Es3\Middleware\CrossDomain;

class Middleware
{
    public static function run(Request $request, Response $response)
    {
        $selef = new self();
        /** 跨域注入 */
        $selef->CrossDomain($request, $response);

        /** 空参数过滤 */
        $selef->clearEmptyParams($request, $response);
    }

    private function CrossDomain(Request $request, $response)
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
}
