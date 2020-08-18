<?php

namespace Es3;

use EasySwoole\Component\Singleton;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;

class CrossDomain extends Config
{
    use Singleton;

    public function run(Request $request, Response $response)
    {
        // 任何环境都不做限制
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, system_id, app_code, token, identity');

        /** 生产情况的跨域 由 运维处理 */
        if (!EsConfig::getInstance()->isProduction()) {

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
}
