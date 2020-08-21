<?php

namespace Es3\Auth;


use App\Constant\AppConst;
use EasySwoole\Component\Di;
use EasySwoole\Component\Singleton;
use EasySwoole\Http\Request;
use EasySwoole\Policy\PolicyNode;
use Es3\EsConfig;

class Policy
{
    use Singleton;

    public function isAuth(): bool
    {
        $isAuth = true;

        $policy = new \EasySwoole\Policy\Policy();
        $policyConf = EsConfig::getInstance()->getConf('policy', true);
        foreach ($policyConf as $key => $conf) {
            $policy->addPath($key, $conf);
        }

        $request = Di::getInstance()->get(AppConst::DI_REQUEST);
        $uri = $request->getServerParams()['request_uri'];
        $iaAuth = $policy->check($uri);
        if ($iaAuth == PolicyNode::EFFECT_ALLOW) {
            $isAuth = false;
        }

        return $isAuth;
    }
}