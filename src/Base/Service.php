<?php

namespace Es3\Base;

trait Service
{
    protected $dao;

    public function get($where = null)
    {
        return $this->getDao()->get($where = null);
    }

    public function all($where = null)
    {
        $this->getDao()->all($where = null);
    }

    public function searchAll(array $params = [], array $searchLinkType = [], array $page = [], array $orderBys = [], array $groupBys = []): array
    {
    }

    public function insert(array $params): int
    {
    }

    public function insertAll(array $params, bool $ignoreEr = false): array
    {
    }

    public function update(array $originalFieldValues, array $updateFieldValues): void
    {
    }

    public function delete(array $fieldValues): void
    {
    }

    public function deletePrimaryKeys(array $ids): void
    {
    }

    public function getAutoIncrement()
    {
    }

    public function setAutoIncrement(int $autoIncrement)
    {
    }

    public function getLast()
    {
    }

    public function truncate()
    {
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
    protected function setDao($dao): void
    {
        $this->dao = $dao;
    }
}
