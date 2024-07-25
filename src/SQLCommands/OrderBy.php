<?php

namespace SophyDB\SQLCommands;

use SophyDB\DML\DML;

final class OrderBy
{
    private DML $dml;

    public function __construct($dml = null)
    {
        $this->dml = $dml;
    }

    public function orderBy($columns, $direction = 'asc')
    {
        $column_string = '';

        if (is_array($columns)) {
            $array_string = [];

            foreach ($columns as $column) {

                if (is_array($column) && count($column) == 2) {
                    $array_string[] = $this->dml->parser->fixColumnName($column[0])['name'] . " " . $column[1];
                } else {
                    $array_string[] = $this->dml->parser->fixColumnName($column)['name'] . " $direction";
                }
            }

            $column_string = implode(',', $array_string);
            $this->dml->binding->addToSourceArray('ORDER_BY', "ORDER BY $column_string");
        } else {
            $column_string = $this->dml->parser->fixColumnName($columns)['name'];
            $this->dml->binding->addToSourceArray('ORDER_BY', "ORDER BY $column_string $direction");
        }

        return $this;
    }
    public function orderByCount($column, $direction = 'asc')
    {
        $column = $this->dml->parser->fixColumnName($column)['name'];
        $this->dml->binding->addToSourceArray('ORDER_BY', "ORDER BY COUNT($column) $direction");
        return $this;
    }

    public function inRandomOrder()
    {
        $this->dml->binding->addToSourceArray('ORDER_BY', "ORDER BY RAND()");
        return $this;
    }

    public function latest($column = 'created_at')
    {
        $this->orderBy($column, 'DESC');
        return $this;
    }

    public function oldest($column = 'created_at')
    {
        $this->orderBy($column, 'ASC');
        return $this;
    }
}
