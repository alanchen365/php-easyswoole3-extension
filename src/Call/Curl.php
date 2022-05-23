<?php

namespace Es3\Call;

use EasySwoole\Component\Di;
use EasySwoole\HttpClient\Bean\Response;
use  EasySwoole\HttpClient\HttpClient;
use Es3\Exception\ErrorException;

class Curl extends HttpClient
{
    protected $is200 = true;

    /**
     * 设置请求头集合
     * @param array $header
     * @param bool $isMerge
     * @param bool strtolower
     * @return HttpClient
     */
    public function setHeaders(array $header, $isMerge = true, $strtolower = false): HttpClient
    {
        $this->clientHandler->getRequest()->setHeaders($header, $isMerge, $strtolower);
        return $this;
    }

    public function get(array $headers = []): Response
    {
        $response = parent::get($headers);
        $this->isSuccess($response);
        if ($this->is200) {
            $this->is200($response);
        }

        return $response;
    }

    public function post($data = null, array $headers = []): Response
    {
        $response = parent::post($data, $headers);
        $this->isSuccess($response);
        if ($this->is200) {
            $this->is200($response);
        }

        return $response;
    }

    public function delete(array $headers = []): Response
    {
        $response = parent::delete($headers);
        $this->isSuccess($response);
        if ($this->is200) {
            $this->is200($response);
        }

        return $response;
    }

    public function put($data = null, array $headers = []): Response
    {
        $response = parent::put($data, $headers);
        $this->isSuccess($response);
        if ($this->is200) {
            $this->is200($response);
        }

        return $response;
    }

    private function isSuccess(Response $response)
    {
        try {
            $errCode = $response->getErrCode();
            if ($errCode !== 0) {
                throw new ErrorException(1021, '远程网络异常:' . $response->getErrMsg());
            }

        } catch (\Throwable $throwable) {

            $trace = $throwable->getTrace()[2] ?? null;
            Di::getInstance()->set(\Es3\Constant\ResultConst::FILE_KEY, $trace['file'] ?? null . $trace['function'] ?? null);
            Di::getInstance()->set(\Es3\Constant\ResultConst::LINE_KEY, $trace['line'] ?? null);

            throw new ErrorException($throwable->getCode(), $throwable->getMessage());
        }
    }

    private function is200(Response $response)
    {
        try {
            $code = $response->getStatusCode();
            if (200 != $code) {
                throw new ErrorException(1020, '远程网络连接失败 http_code:' . $code . ' ' . $response->getBody());
            }

        } catch (\Throwable $throwable) {

            $trace = $throwable->getTrace()[2] ?? null;
            Di::getInstance()->set(\Es3\Constant\ResultConst::FILE_KEY, $trace['file'] ?? null . $trace['function'] ?? null);
            Di::getInstance()->set(\Es3\Constant\ResultConst::LINE_KEY, $trace['line'] ?? null);

            throw new ErrorException($throwable->getCode(), $throwable->getMessage());
        }
    }

    /**
     * @param bool $is200
     */
    public function setIs200(bool $is200): void
    {
        $this->is200 = $is200;
    }
}