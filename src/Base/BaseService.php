<?php

namespace Es3\Base;

class BaseService
{
    protected $dao;

    /**
     * 获取dao
     * @return mixed
     */
    public function getDao()
    {
        return $this->dao;
    }

    /**
     * 设置dao
     * @param mixed $dao
     */
    public function setDao($dao): void
    {
        $this->dao = $dao;
    }

}