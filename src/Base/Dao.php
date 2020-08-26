<?php

namespace Es3\Base;

use App\Constant\ResultConst;
use Es3\Exception\ErrorException;
use EasySwoole\Mysqli\QueryBuilder;

trait Dao
{
    public $model;

    public function save(array $params)
    {
        /** 调整参数 */
        $params = $this->adjustWhere($params);
        return $this->model::create($params)->save();
    }

    public function deleteField(array $data): int
    {
        if (superEmpty($data)) {
            throw new ErrorException(1010, "deleteField()删除参数不能为空");
        }
        $this->model = $this->model::create();
        $res = $this->model->delete($data, true);
        return intval($res);
    }

    public function updateField(array $originalFieldValues, array $updateFieldValues, $allow = false): int
    {
        $originalFieldValues = $this->adjustWhere($originalFieldValues);
        if (superEmpty($originalFieldValues) || superEmpty($updateFieldValues)) {
            throw new ErrorException(1011, "updateField()更新参数不能为空");
        }

        $schemaInfo = $this->model->schemaInfo();
        $primaryKey = $schemaInfo->getPkFiledName();

        // 不允许更新主键
        unset($updateFieldValues[$primaryKey]);

        $this->model = $this->model::create();
        $this->model->update($updateFieldValues, $originalFieldValues, $allow);

        $lastErrorNo = $this->model->lastQueryResult()->getLastErrorNo();
        if ($lastErrorNo !== 0) {
            throw new ErrorException(1005, $model->lastQueryResult()->getLastError());
        }

        return intval($this->model->lastQueryResult()->getAffectedRows());
    }

    public function get(array $where = [], array $field = [])
    {
        $LogicDelete = $this->model->getLogicDelete();
        $where = array_merge($where, $LogicDelete);

        return $this->model::create()->field($field)->get($where);
    }

    public function delete($where = null, $allow = false): int
    {
        $this->model = $this->model::create();
        $this->model->delete($where, $allow);

        $lastErrorNo = $this->model->lastQueryResult()->getLastErrorNo();
        if ($lastErrorNo !== 0) {
            throw new ErrorException(1004, $model->lastQueryResult()->getLastError());
        }

        return intval($this->model->lastQueryResult()->getAffectedRows());
    }

    public function update(array $data = [], array $primary, $allow = false): int
    {
        $schemaInfo = $this->model->schemaInfo();
        $primaryKey = $schemaInfo->getPkFiledName();

        // 不允许更新主键
        unset($data[$primaryKey]);

        /** 调整参数 */
        $data = $this->adjustWhere($data);

        $this->model = $this->model::create();
        $this->model->update($data, $primary, $allow);

        $lastErrorNo = $this->model->lastQueryResult()->getLastErrorNo();
        if ($lastErrorNo !== 0) {
            throw new ErrorException(1005, $model->lastQueryResult()->getLastError());
        }

        return intval($this->model->lastQueryResult()->getAffectedRows());
    }

    public function getLast(string $field = 'id'): ?array
    {
        $row = $this->model::create()->order($field, 'DESC')->get();
        return $row->toArray() ?? [];
    }

    public function getAutoIncrement(): ?int
    {
        $schemaInfo = $this->model->schemaInfo();
        $res = $this->model::create()->query((new QueryBuilder())->raw("select auto_increment from information_schema.tables where table_name = '" . $schemaInfo->getTable() . "'"));

        return $res[0]['auto_increment'] ?? null;
    }

    public function setAutoIncrement(int $autoIncrement): void
    {
        $schemaInfo = $this->model->schemaInfo();
        $this->model::create()->query((new QueryBuilder())->raw('alter table ' . $schemaInfo->getTable() . ' auto_increment = ' . $autoIncrement));
    }

    public function getAll($where = null, array $page = [], array $orderBys = ['id' => 'DESC'], array $groupBys = [], array $field = [])
    {
        $model = $this->model::create();

        $LogicDelete = $this->model->getLogicDelete();
        $where = array_merge($where, $LogicDelete);
        $where = $this->adjustWhere($where);

        if ($page) {
            $model->limit($page[0], $page[1]);
        }

        if ($orderBys) {
            foreach ($orderBys as $field => $orderBy) {
                $model->order($field, $orderBy);
            }
        }

        if ($groupBys) {
            foreach ($groupBys as $field) {
                $model->group($field);
            }
        }

        $list = $model->field($field)->withTotalCount()->all($where);
        $total = $model->lastQueryResult()->getTotalCount();

        return [ResultConst::RESULT_LIST_KEY => $list, ResultConst::RESULT_TOTAL_KEY => $total];
    }

    public function truncate(): void
    {
        $schemaInfo = $this->model->schemaInfo();
        $this->model = $this->model::create();
        $this->model->query((new QueryBuilder())->raw('TRUNCATE TABLE ' . $schemaInfo->getTable()));
    }

    public function insertAll(array $data, $replace = true, $transaction = true, $returnField = 'id'): array
    {
        $this->model = $this->model::create();
        $res = $this->model->saveAll($data, $replace, $transaction);
        $res = json_decode(json_encode($res), true);
        return [$returnField => array_column($res, $returnField)];
    }

    /**
     * 清理where条件
     */
    public function adjustWhere(array $params, $isLogicDelete = true): array
    {
        return $this->model->adjustWhere($params, $isLogicDelete);
    }

    public function getLogicDelete(): array
    {
        return $this->model->getLogicDelete();
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model): void
    {
        $this->model = $model;
    }
}
