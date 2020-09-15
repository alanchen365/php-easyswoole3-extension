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

class Json
{
    /**
     * HTTP 返回成功
     * @param Response $response
     * @param \Es3\Output\Result $result
     * @param int $code
     * @param string $msg
     */
    public static function success(int $code = ResultConst::SUCCESS_CODE, string $msg = ResultConst::SUCCESS_MSG): void
    {
        Di::getInstance()->get(AppConst::DI_RESULT)->setTrace(debug_backtrace());
        Json::setBody($code, $msg, true);
    }

    /**
     * HTTP 返回失败
     * @param Response $response
     * @param Results $results
     * @param int $code
     * @param string $msg
     */
    public static function fail(\Throwable $throwable, int $code = ResultConst::FAIL_CODE, string $msg = ResultConst::FAIL_MSG): void
    {
        Di::getInstance()->get(AppConst::DI_RESULT)->setTrace($throwable->getTrace());
        Json::setBody($code, $msg, false);
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
        $response = Di::getInstance()->get(AppConst::DI_RESPONSE);
        $result = Di::getInstance()->get(AppConst::DI_RESULT);

        /** 返回数据定制*/
        $code = $isSuccess ? (empty($code) ? ResultConst::SUCCES_CODE : $code) : (empty($code) ? 0 : $code);

        /** 写入返回信息 */
        $result->setMsg(strval($msg));
        $result->setCode(intval($code));

        $data = $result->toArray();

        $response->withHeader('Content-type', 'application/json;charset=utf-8');
        $response->write(json_encode($data));
        $response->end();
    }
}
