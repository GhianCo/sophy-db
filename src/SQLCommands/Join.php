<?php

namespace SophyDB\SQLCommands;

use SophyDB\DML\DML;

final class Join
{
    private DML $dml;

    public function __construct($dml = null)
    {
        $this->dml = $dml;
    }

    public function join(...$args)
    {
        $query = $this->queryMakerJoin('INNER', $args);
        $this->dml->binding->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function leftJoin(...$args)
    {
        $query = $this->queryMakerJoin('LEFT', $args);
        $this->dml->binding->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function rightJoin(...$args)
    {
        $query = $this->queryMakerJoin('RIGHT', $args);
        $this->dml->binding->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function fullJoin(...$args)
    {
        $query = $this->queryMakerJoin('FULL', $args);
        $this->dml->binding->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function crossJoin($column)
    {
        $this->dml->binding->addToSourceArray('JOIN', "CROSS JOIN `$column`");
        return $this;
    }

    public function queryMakerJoin($type, $args)
    {
        $join_table = $args[0];
        $join_table_column = $args[1];
        $operator = $args[2] ?? false;
        $main_column = $args[3] ?? false;

        if (!$operator && !$main_column) {
            $table_second = $this->dml->parser->fixColumnName($join_table);
            $table_main = $this->dml->parser->fixColumnName($join_table_column);

            $join_table = $table_second['table'];

            $join_table_column = $table_second['name'];

            $operator = '=';

            $main_column = $table_main['name'];
        } else if ($operator && !$main_column) {
            $table_second = $this->dml->parser->fixColumnName($join_table);
            $table_main = $this->dml->parser->fixColumnName($operator);

            $operator = $join_table_column;

            $join_table = $table_second['table'];
            $join_table_column = $table_second['name'];

            $main_column = $table_main['name'];
        } else if ($main_column) {
            $join_table = "`$join_table`";

            $join_table_column = $this->dml->parser->fixColumnName($join_table_column)['name'];
            $main_column = $this->dml->parser->fixColumnName($main_column)['name'];
        }

        return "$type JOIN $join_table ON $join_table_column $operator $main_column";
    }
}
