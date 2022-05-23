<?php

namespace Es3\Exception;


use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Logger;
use Es3\Constant\ResultConst;

class BaseException extends \Exception
{
    protected $category;

    public function __construct(int $code, string $msg = '', \Throwable $previous = null)
    {
        // 写入出错的行号和文件
        if (!Di::getInstance()->get(ResultConst::FILE_KEY)) {
            Di::getInstance()->set(ResultConst::FILE_KEY, $this->getFile());
        }

        if (!Di::getInstance()->get(ResultConst::LINE_KEY)) {
            Di::getInstance()->set(ResultConst::LINE_KEY, $this->getLine());
        }

        Di::getInstance()->set(ResultConst::TRACE_KEY, $this->getTrace());

        // 写入日志
//        $msg = "运行出现异常 错误号:{$code} 错误信息:{$msg}";
        $category = $this->category;
        Logger::getInstance()->$category($msg, 'exception');

        parent::__construct($msg, $code, $this);
    }
}