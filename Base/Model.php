<?php

namespace Es3\Base;

use App\Constant\AppConst;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Utility\Schema\Table;
use Es3\EsUtility;

class Model extends AbstractModel
{
    protected $autoTimeStamp = false;

    protected $createTime = false;

    protected $updateTime = false;

    /**
     * 调整where条件
     */
    public function adjustWhere(array $params): array
    {
        $schemaInfo = $this->schemaInfo();
        $columns = $schemaInfo->getColumns();

        foreach ($params as $field => $value) {
            if (!isset($columns[$field])) {
                unset($params[$field]);
            }
        }

        return $params;
    }

    /**
     * 获取逻辑标志
     */
    public function getLogicDelete(string $value = '0'): array
    {
        $schemaInfo = $this->schemaInfo();
        $columns = $schemaInfo->getColumns();

        foreach (AppConst::TABLE_LOGIC_DELETE as $field) {
            if (isset($columns[$field])) {
                return [$field => $value];
            }
        }
        
        return [];
    }

    /**
     * 重写删除方法 为了兼容逻辑删除
     */
    public function delete($where = null, $allow = false): int
    {
        /** 如果有逻辑删除标识 就添加上条件 */
        $LogicDelete = $this->getLogicDelete('1');

        if (empty($LogicDelete)) {
            $count = intval(parent::destroy($where, $allow));
        } else {
            $count = intval(parent::update($LogicDelete, $where));
        }

        return $count;
    }
}
