<?php

namespace Es3\Output;

use App\Constant\AppConst;
use App\Constant\ResultConst;
use EasySwoole\Component\Di;
use Es3\EsConfig;

class Result
{
    private $_code;
    private $_msg;
    private $_result;
    private $_trace;

    public function __construct()
    {
    }

    /**
     * @param mixed $code
     */
    public function setCode(int $code): void
    {
        $this->_code = $code;
    }

    /**
     * @return mixed
     */
    public function getTrace()
    {
        return $this->_trace;
    }

    /**
     * @param mixed $trace
     */
    public function setTrace($trace): void
    {
        $this->_trace = $trace;
    }

    /**
     * @param mixed $msg
     */
    public function setMsg(string $msg): void
    {
        $this->_msg = $msg;
    }

    public function set(string $key, $value, bool $overlay = true): void
    {
        //key为空或不是字串
        if (!$key) {
            return;
        }
        //禁止覆盖
        if ((!$overlay) && isset($this->_result[$key])) {
            return;
        }
        $this->_result[$key] = $value;
    }

    public function toArray(): array
    {
        $result = empty($this->_result) ? (object)[] : $this->_result;

        $data = [
            ResultConst::CODE_KEY => $this->_code,
            ResultConst::DATE_KEY => $result,
            ResultConst::MSG_KEY => $this->_msg,
            ResultConst::TRACE_KEY => $this->_trace,
            ResultConst::TIME_KEY => date(ResultConst::TIME_FORMAT),
            AppConst::ID_KEY => Di::getInstance()->get(AppConst::ID_KEY),
        ];

        /** 不是生产环境 显示调试信息 */
        if (isProduction()) {
            unset($data['trace']);
        }
        
        if (empty($this->_result)) {
            unset($data['data']);
        }

        return $data;
    }

    public function del($key): void
    {
        if (isset($this->_result[$key])) {
            unset($this->_result[$key]);
        }

        return;
    }

    public function clear(): void
    {
        foreach ((array)$this->_result as $key => $item) {
            unset($this->_result[$key]);
        }
    }
}
