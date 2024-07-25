<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\DML;

class Insert
{
    public DML $dml;

    public function __construct(DML $dml)
    {
        $this->dml = $dml;
    }
    public function insert(array $values, $get_last_insert_id = false)
    {
        $this->dml->setAction('insert');
        $query = $this->makeInsertQueryString($values);
        $result = $this->dml->execute($query, $this->dml->binds);

        if (!$get_last_insert_id) {
            return $result;
        } else {
            return $this->dml->conn->pdo()->lastInsertId();
        }
    }

    public function insertGetId(array $values)
    {
        return $this->insert($values, true);
    }

    protected function makeInsertQueryString(array $values)
    {
        $table = $this->dml->table;
        $param_name = [];
        $param_value_name_list = [];

        foreach ($values as $name => $value) {
            $param_name[] = $this->dml->parser->fixColumnName($name)['name'];
            $param_value_name_list[] = $this->dml->binding->addToParamAutoName($value);
        }

        return "INSERT INTO `$table` (" . implode(',', $param_name) . ") VALUES (" . implode(',', $param_value_name_list) . ")";
    }
}
