<?php

namespace Es3\Base;


trait Model
{
    /**
     * 清理where条件
     */
    public function clearWhere(array $params): array
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
}
