<?php

namespace Es3\Output;

use App\AppConst\AppInfo;
use App\Constant\AppConst;
use App\Constant\ResultConst;
use AsaEs\AsaEsConst;
use AsaEs\Logger\FileLogger;
use EasySwoole\Config;
use EasySwoole\Component\Di;
use EasySwoole\Core\Swoole\Task\TaskManager;
use EasySwoole\Http\Response;
use Es3\EsConfig;
use Es3\Output\Result;

class Http
{
    /**
     * HTTP 返回成功
     * @param Response $response
     * @param \Es3\Output\Result $result
     * @param int $code
     * @param string $msg
     */
    public static function success(int $code = ResultConst::SUCCES_CODE, string $msg = ''): void
    {
        Http::setBody($code, $msg, true);
    }

    /**
     * HTTP 返回失败
     * @param Response $response
     * @param Results $results
     * @param int $code
     * @param string $msg
     */
    public static function fail(\Throwable $throwable, int $code = ResultConst::FAIL_CODE, string $msg = ''): void
    {
        Di::getInstance()->set(AppConst::DI_THROWABLE, $throwable);
        Http::setBody($code, $msg, false);
    }

    /**
     * HTTP 返回结构
     * @param Response $response
     * @param \Es3\Output\Result $result
     * @param int $code
     * @param string $msg
     * @param $isSuccess
     */
    private static function setBody(int $code, string $msg = '', bool $isSuccess): void
    {
        $throwable = Di::getInstance()->get(AppConst::DI_THROWABLE);
        $response = Di::getInstance()->get(AppConst::DI_RESPONSE);
        $result = Di::getInstance()->get(AppConst::DI_RESULT);

        /** 返回数据定制*/
        $code = $isSuccess ? (empty($code) ? ResultConst::SUCCES_CODE : $code) : (empty($code) ? 0 : $code);

        /** 写入返回信息 */
        $result->setMsg(strval($msg));
        $result->setCode(intval($code));
        $data = $result->toArray();

        /** 不是生产环境 显示调试信息 */
        if (!EsConfig::getInstance()->isProduction() && !$isSuccess && $throwable) {
            $data['trace'] = $throwable->getTrace();
        }
        
        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->write(json_encode($data));
        $response->end();
    }
}
