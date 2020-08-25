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
        return $this->dao->save($params);
    }

    public function delete($where = null, $allow = false): int
    {
        return intval($this->dao->delete($where, $allow));
    }

    public function get(array $where = [], array $field = [])
    {
        return $this->dao->get($where, $field);
    }

    public function update(array $data = [], $where = null, $allow = false): int
    {
        return intval($this->dao->update($data, $where, $allow));
    }

    /**
     * get all
     * @param null $where
     * @param array $page
     * @param array $orderBys
     * @param array $groupBys
     * @return mixed
     */
    public function getAll($where = null, array $page = [], array $orderBys = [], array $groupBys = [], $field = [])
    {
        return $this->dao->getAll($where, $page, $orderBys, $groupBys, $field);
    }

    /**
     * 按照某一列排序 获取最后一条记录
     */
    public function getLast(string $field = 'id'): ?array
    {
        
    }

    /**
     * 更新全表中某列等于某个值的所有数据
     */
    public function updateField(array $originalFieldValues, array $updateFieldValues): void
    {

    }

    /**
     * 批量插入
     * @param array $params
     * @return array
     */
    public function insertAll(array $params): array
    {

        [
            ['id' => '1'],
            ['id' => '2'],
        ];

        $ids = [];

        return $ids;
    }

    /**
     * 根据某列删除
     * @return int
     */
    public function deleteField(): int
    {

        $count = 0;
        return intval($count);
    }

    public function query()
    {

    }

    /**
     * 截断表
     */
    public function truncate(): void
    {

    }

    /**
     * 获取自增编号
     */
    public function getAutoIncrement(): int
    {

    }

    /**
     * 设置自增编号
     * 会自动提交事物
     */
    public function setAutoIncrement()
    {

    }

    /**
     * 清理where条件
     */
    public function adjustWhere(array $params, $isLogicDelete = true): array
    {
        return $this->dao->adjustWhere($params, $isLogicDelete);
    }

    public function getLogicDelete(): array
    {
        return $this->dao->getLogicDelete();
    }

    /**
     * @return mixed
     */
    public function getDao()
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
