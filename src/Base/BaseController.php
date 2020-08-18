<?php

namespace Es3\Base;

use App\Constant\AppConst;
use EasySwoole\Component\Di;
use EasySwoole\EasySwoole\Core;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Exception\ParamAnnotationValidateError;
use EasySwoole\Http\Message\Status;
use EasySwoole\Trigger\Trigger;
use EasySwoole\Validate\Validate;
use Es3\AutoNew;
use Es3\EsConfig;
use Es3\EsUtility;
use Es3\Exception\WaringException;
use Es3\Output\Json;
use Es3\Proxy\DaoProxy;
use Es3\Proxy\ModelProxy;
use Es3\Proxy\ServiceProxy;
use Es3\Proxy\ValidateProxy;

class BaseController extends Controller
{
    protected $service;

    /**通用验证器 */
    protected function onRequest(?string $action): ?bool
    {
        try {
            /** 验证器代理 */
            $validateProxy = new ValidateProxy(get_called_class());
            $validateProxy->validate($action, $this->getParams());

            /** service 代理 */
            $serviceProxy = new ServiceProxy(get_called_class());
            $service = $serviceProxy->getService();
            if ($service) {
                $this->setService($service);
            }

            /** dao 代理 */
            $daoProxy = new DaoProxy(get_called_class());
            $dao = $daoProxy->getDao();

            if ($dao && $service) {
                $service->setDao($dao);
            }

            /** model 代理 */
            $modelProxy = new ModelProxy(get_called_class());
            $model = $modelProxy->getModel();

            if ($model && $dao) {
                $dao->setModel($model);
            }

            return true;
        } catch (\Throwable $throwable) {
            Json::fail($throwable, $throwable->getCode(), $throwable->getMessage());
            return false;
        }

        return true;
    }

    public function getParams(): array
    {
        $params = $this->request()->getRequestParam();
        $raw = $this->request()->getBody()->__toString();
        $rawParams = json_decode($raw, true);

        if ($rawParams) {
            $params = $rawParams + $params;
        }

        return $params;
    }

    protected function onException(\Throwable $throwable): void
    {
        throw $throwable;
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(406);
        $this->response()->end();
    }

    /**
     * 获取分页
     */
    public function getPage(): array
    {
        $params = $this->getParams();

        $pageNo = $params[AppConst::PAGE_NO_KEY] ?? 0;
        $pageNum = $params[AppConst::PAGE_NUM_KEY] ?? AppConst::PAGE_DEFAULT_NUM;

        // 前端不传递分页 给个默认
        if (0 === $pageNo) {
            return [0, intval($pageNum)];
        }

        $pageNo = $pageNo > 0 ? $pageNo : 1;
        $offset = ($pageNo - 1) * $pageNum;

        return [intval($offset), intval($pageNum)];
    }

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }
}
