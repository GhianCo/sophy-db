<?php

namespace SophyDB\SQLCommands;

use SophyDB\SQLCommands\MySQL\Select;

final class Having
{
    private Select $select;
    
    public function __construct($select = null)
    {
        $this->select = $select;
    }

    /**
     * Add a "having" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @param  string  $boolean
     * @return $this
     */

    public function having($column, $operator, $value = null, $boolean = 'and', $fn = '')
    {
        $this->addOperatorHaving($boolean);
        $this->select->parser->fixOperatorAndValue($operator, $value);
        $column = $this->select->parser->fixColumnName($column)['name'];

        $array = $this->select->binding->getSourceValueItem('HAVING');
        $beginning = 'HAVING';

        if (count($array) > 0) {
            $beginning = '';
        }

        if (empty($fn)) {
            $this->select->binding->addToSourceArray('HAVING', "$beginning $column $operator $value");
        } else {
            $this->select->binding->addToSourceArray('HAVING', "$beginning $fn($column) $operator $value");
        }

        return $this;
    }

    /**
     * Add a "or having" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function orHaving($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR');
    }

    /**
     * Add a "having count()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    public function havingCount($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'AND', 'COUNT');
    }

    /**
     * Add a "having sum()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    public function havingSum($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'AND', 'SUM');
    }

    /**
     * Add a "having avg()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    public function havingAvg($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'AND', 'AVG');
    }

    /**
     * Add a "or having count()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    public function orHavingCount($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR', 'COUNT');
    }

    /**
     * Add a "or having sum()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    public function orHavingSum($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR', 'SUM');
    }

    /**
     * Add a "or having avg()" clause to the query.
     *
     * @param  string  $column
     * @param  string|null  $operator
     * @param  string|null  $value
     * @return $this
     */
    
    public function orHavingAvg($column, $operator, $value = null)
    {
        return $this->having($column, $operator, $value, 'OR', 'AVG');
    }

    public function havingRaw($sql, array $bindings = [], $boolean = 'AND')
    {
        $this->addOperatorHaving($boolean);

        $array = $this->select->binding->getSourceValueItem('HAVING');
        $beginning = 'HAVING';

        if (count($array) > 0) {
            $beginning = '';
        }
        $raw = $this->select->raw($sql, $bindings);
        $raw = $this->select->makeRaw($raw->getRawQuery(), $raw->getRawValues());
        $this->select->binding->addToSourceArray('HAVING', "$beginning " . $raw);

        return $this;
    }

    public function orHavingRaw($sql, array $bindings = [])
    {
        return $this->havingRaw($sql, $bindings, 'OR');
    }

    public function addOperatorHaving($operator)
    {
        $array = $this->select->binding->getSourceValueItem('HAVING');

        if (count($array) > 0) {

            $end = $array[count($array) - 1];

            if (in_array($end, ['AND', 'OR', '(']) == false) {
                $this->select->binding->addToSourceArray('HAVING', $operator);
            }
        }
    }
}