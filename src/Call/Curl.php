<?php

namespace Es3\Call;

use  EasySwoole\HttpClient\HttpClient;

class Curl extends HttpClient
{

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

}