<?php

namespace Es3\Auth;

use App\Constant\AppConst;
use EasySwoole\EasySwoole\Logger;
use Es3\EsConfig;
use Es3\Exception\InfoException;

/**
 * 配置自动加载
 * Class HttpRouter
 * @package Es3\Autoload
 */
class Jwt
{
    public function decode(?string $identity, ?string $key, ?string $alg): array
    {
        try {
            if (superEmpty($identity)) {
                throw new InfoException(1008, '身份信息缺失');
            }

            if (superEmpty($key) || superEmpty($alg)) {
                throw new InfoException(1008, '关键身份信息缺失');
            }

            $token = \Firebase\JWT\JWT::decode($identity, $key, [$alg]);
            return (array)$token;
        } catch (\Throwable $throwable) {
            throw $throwable;
        }
    }

    public function encode(array $data = [], ?string $key, ?string $alg): string
    {
        try {
            if (superEmpty($key) || superEmpty($alg)) {
                throw new InfoException(1008, '关键身份信息缺失');
            }

            $identity = \Firebase\JWT\JWT::encode($data, $key, $alg);

            if (superEmpty($identity)) {
                throw new InfoException(1008, '身份生成失败');
            }

            return strval($identity);
        } catch (\Exception $e) {
            Logger::getInstance()->notice('鉴权错误 : ' . $throwable->getMessage(), 'auth');
            throw new SignException($e->getCode(), $e->getMessage());
        }
    }
}
