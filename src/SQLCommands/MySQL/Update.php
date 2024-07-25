<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\DML;

class Update
{
    public DML $dml;

    public function __construct(DML $dml)
    {
        $this->dml = $dml;
    }

    public function update(array $values)
    {
        $this->dml->setAction('update');
        $this->dml->binding->clearSource('DISTINCT');
        $query = $this->makeUpdateQueryString($values);
        return $this->dml->execute($query, $this->dml->binds);
    }

    public function increment(string $column, int $value = 1)
    {
        $query = $this->makeUpdateQueryIncrement($column, $value);
        return $this->dml->execute($query, $this->dml->binds);
    }


    public function decrement(string $column, int $value = 1)
    {
        $query = $this->makeUpdateQueryIncrement($column, $value, '-');
        return $this->dml->execute($query, $this->dml->binds);
    }

    protected function makeUpdateQueryString(array $values)
    {
        $table = $this->dml->table;
        $params = [];

        foreach ($values as $name => $value) {
            $params[] = $this->dml->parser->fixColumnName($name)['name'] . ' = ' . $this->dml->binding->addToParamAutoName($value);
        }

        $extra = $this->dml->binding->makeSourceValueString();

        return "UPDATE `$table` SET " . implode(',', $params) . " $extra";
    }

    protected function makeUpdateQueryIncrement(string $column, $value = 1, $action = '+')
    {
        $table = $this->dml->table;
        $values = [];

        $column = $this->dml->parser->fixColumnName($column)['name'];

        $params = [];
        $params[] = "$column = $column $action $value";

        foreach ($values as $name => $value) {
            $params[] = $this->dml->parser->fixColumnName($name)['name'] . ' = ' . $this->dml->binding->addToParamAutoName($value);
        }

        $extra = $this->dml->binding->makeSourceValueString();

        return "UPDATE `$table` SET " . implode(',', $params) . " $extra";
    }
}
