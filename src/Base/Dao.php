<?php

namespace Es3\Base;

trait Dao
{
    public function get($where = null)
    {
        return $this->getModel()->get($where);
    }

    public function all($where = null)
    {
        return $this->getModel()->all($where);
    }

    public $model;

    /**
     * @return mixed
     */
    private function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    protected function setModel($model): void
    {
        $this->model = $model;
    }
}
