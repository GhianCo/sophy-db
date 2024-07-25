<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\DML;

class AggregateFn
{
    private DML $dml;

    public function __construct($dml = null)
    {
        $this->dml = $dml;
    }

    public function fn($type, $column)
    {
        if ($column != '*') {
            $column = $this->dml->parser->fixColumnName($column)['name'];
        }

        $this->dml->select->stringArray[] = "$type($column)";
    }

    public function field($column)
    {
        $column = $this->dml->parser->fixColumnName($column)['name'];
        $this->dml->select->stringArray[] = $column;
        return new AsField($this->dml->select);
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return int
     */
    public function count($column = '*')
    {
        $this->fn("COUNT", $column);
        return new AsField($this->dml->select);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function sum($column = '*')
    {
        $this->fn("SUM", $column);
        return new AsField($this->dml->select);
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function avg($column = '*')
    {
        $this->fn("AVG", $column);
        return new AsField($this->dml->select);
    }


    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function max($column)
    {
        $this->fn("MAX", $column);
        return new AsField($this->dml->select);
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function min($column)
    {
        $this->fn("MIN", $column);
        return new AsField($this->dml->select);
    }
}
