<?php

namespace Es3\Base;

use App\Constant\ResultConst;
use App\Module\Owtb\Model\DepotModel;

trait Dao
{
    public $model;

    public function save(array $params)
    {
        $this->model::create($params)->save();
    }

    public function get($where = null)
    {
        return $this->model::create()->get($where);
    }

    public function destroy($where = null, $allow = false)
    {
        return $this->model::create()->destroy($where, $allow);
    }

    public function update(array $data = [], $where = null, $allow = false)
    {
        return $this->model::create()->update($data, $where, $allow);
    }

    public function getAll($where = null, array $page = [], array $orderBys = ['id' => 'DESC'], array $groupBys = [])
    {
        $model = $this->model::create();
        $model = DepotModel::create();

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

        $list = $model->all($where);
        $total = $model->lastQueryResult()->getTotalCount();
        
        return [ResultConst::RESULT_LIST_KEY => $list, ResultConst::RESULT_TOTAL_KEY => $total];
    }

    /**
     * 清理where条件
     */
    public function clearWhere(array $params): array
    {
        return $this->model->clearWhere($params);
    }

    private function getModel()
    {
        return $this->model;
    }

    public function setModel($model): void
    {
        $this->model = $model;
    }
}
