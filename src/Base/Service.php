<?php

namespace Es3\Base;

use App\Constant\AppConst;
use EasySwoole\Component\Di;
use Es3\Exception\WaringException;

trait Service
{
    protected $dao;

    public function save(array $params)
    {
        $this->dao->save($params);
    }

    public function destroy($where = null, $allow = false)
    {
        return $this->dao->destroy($where, $allow);
    }

    public function get($where = null)
    {
        return $this->dao->get($where);
    }

    public function update(array $data = [], $where = null, $allow = false)
    {
        return $this->dao->update($data, $where, $allow);
    }

    public function getAll($where = null, array $page = [], array $orderBys = [], array $groupBys = [])
    {
        return $this->dao->getAll($where, $page, $orderBys, $groupBys);
    }

    /**
     * 清理where条件
     */
    public function clearWhere(array $params): array
    {
        return $this->dao->clearWhere($params);
    }

    /**
     * @return mixed
     */
    private function getDao()
    {
        return $this->dao;
    }

    /**
     * @param mixed $dao
     */
    public function setDao($dao): void
    {
        $this->dao = $dao;
    }
}
