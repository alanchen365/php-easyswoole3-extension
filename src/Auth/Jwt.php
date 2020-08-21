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
    public function decode(?string $identity): array
    {
        try {
            $jwtConf = EsConfig::getInstance()->getConf('jwt', true);
            $secretKey = $jwtConf['key'] ?? null;
            $alg = $jwtConf['alg'] ?? null;

            if (superEmpty($identity)) {
                throw new InfoException(1008, '身份信息缺失');
            }

            if (superEmpty($jwtConf) || superEmpty($secretKey)) {
                throw new InfoException(1008, '关键身份信息缺失');
            }

            $token = \Firebase\JWT\JWT::decode($identity, $secretKey, [$alg]);

            return (array)$token;
        } catch (\Throwable $throwable) {
            Logger::getInstance()->notice('鉴权错误 : ' . $throwable->getMessage(), 'auth');
            throw $throwable;
        }
    }

    public static function encode(array $data = []): string
    {
        try {
            $jwtConf = EsConfig::getInstance()->getConf('jwt', true);
            $secretKey = $jwtConf['key'] ?? null;
            $alg = $jwtConf['alg'] ?? null;

            if (superEmpty($jwtConf) || superEmpty($secretKey)) {
                throw new InfoException(1008, '关键身份信息缺失');
            }

            $token = JWT::encode($data, $secretKey, $alg);

            if (superEmpty($token)) {
                throw new InfoException(1008, '身份生成失败');
            }

            return strval($token);
        } catch (\Exception $e) {
            Logger::getInstance()->notice('鉴权错误 : ' . $throwable->getMessage(), 'auth');
            throw new SignException($e->getCode(), $e->getMessage());
        }
    }
}
