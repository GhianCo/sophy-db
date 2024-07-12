<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\Parser;

trait AggregateFn
{
    use Parser;

    public function fn($type, $column)
    {
        if ($column != '*') {
            $column = $this->fixColumnName($column)['name'];
        }

        $this->stringArray[] = "$type($column)";
    }

    public function field($column)
    {
        $column = $this->fixColumnName($column)['name'];
        $this->stringArray[] = $column;
        return new AsField($this);
    }


    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return int
     */
    public function countFn($column = '*')
    {
        $this->fn("COUNT", $column);
        return new AsField($this);
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function sumFn($column = '*')
    {
        $this->fn("SUM", $column);
        return new AsField($this);
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function avgFn($column = '*')
    {
        $this->fn("AVG", $column);
        return new AsField($this);
    }


    /**
     * Retrieve the maximum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function maxFn($column)
    {
        $this->fn("MAX", $column);
        return new AsField($this);
    }

    /**
     * Retrieve the minimum value of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function minFn($column)
    {
        $this->fn("MIN", $column);
        return new AsField($this);
    }
}
