<?php

namespace Es3\Auth;


use App\Constant\AppConst;
use EasySwoole\Component\Di;
use EasySwoole\Component\Singleton;
use EasySwoole\EasySwoole\Command\Utility;
use EasySwoole\FastCache\Cache;
use EasySwoole\Http\Request;
use EasySwoole\Policy\PolicyNode;
use Es3\EsConfig;

class Policy
{
    use Singleton;

    public function initialize(): \EasySwoole\Policy\Policy
    {
        $policy = new \EasySwoole\Policy\Policy();
        $isAuthKey = 'policy.' . AppConst::CONF_IS_AUTH;
        $policyConf = EsConfig::getInstance()->getConf($isAuthKey, true);
        foreach ($policyConf as $key => $conf) {
            $policy->addPath($key, $conf);
        }

        echo Utility::displayItem($isAuthKey, json_encode($policyConf));
        echo "\n";
        
        return $policy;
    }

    /**
     * 白名单策略
     * @return bool
     * @throws \Throwable
     */
    public function isAuth(): bool
    {
        $isAuth = true;

        $policy = Di::getInstance()->get(AppConst::DI_POLICY);
        $request = Di::getInstance()->get(AppConst::DI_REQUEST);

        $uri = $request->getServerParams()['request_uri'];
        $iaAuth = $policy->check($uri);

        if ($iaAuth == PolicyNode::EFFECT_ALLOW) {
            $isAuth = false;
        }

        return $isAuth;
    }
}
