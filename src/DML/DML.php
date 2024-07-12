<?php

namespace SophyDB\DML;

use SophyDB\SQLCommands\Keywords;
use SophyDB\SQLCommands\MySQL\Select;

final class DML
{
    use Select;

    protected $sourceValue = [];
    protected $conn;

    public function setConnection($conn)
    {
        $this->conn = $conn;
    }

    protected function clearSource($struct_name)
    {
        $s_index = Keywords::get($struct_name);
        $this->sourceValue[$s_index] = [];
    }

    protected function addToSourceArray($struct_name, $value)
    {
        $s_index = Keywords::get($struct_name);
        $this->sourceValue[$s_index][] = $value;
    }

    public function getSourceValueItem($struct_name)
    {
        $s_index = Keywords::get($struct_name);
        return $this->sourceValue[$s_index] ?? [];
    }

    public function where(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhere($args);
        return $this;
    }

    public function orWhere(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhere($args);
        return $this;
    }

    public function whereNot(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhere($args, 'NOT');
        return $this;
    }

    public function orWhereNot(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhere($args, 'NOT');
        return $this;
    }

    public function whereNull($name)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereStaticValue($name, 'IS NULL');
        return $this;
    }

    public function orWhereNull($name)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereStaticValue($name, 'IS NULL');
        return $this;
    }

    public function whereNotNull($name)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereStaticValue($name, 'IS NOT NULL');
        return $this;
    }

    public function orWhereNotNull($name)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereStaticValue($name, 'IS NOT NULL');
        return $this;
    }


    public function whereBetween($name, array $values)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereBetween($name, $values);
        return $this;
    }

    public function orWhereBetween($name, array $values)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereBetween($name, $values);
        return $this;
    }

    public function whereNotBetween($name, array $values)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereBetween($name, $values, 'NOT');
        return $this;
    }

    public function orWhereNotBetween($name, array $values)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereBetween($name, $values, 'NOT');
        return $this;
    }

    public function whereDate(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereLikeDate('DATE', $args);
        return $this;
    }

    public function orWhereDate(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereLikeDate('DATE', $args);
        return $this;
    }

    public function whereYear(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereLikeDate('YEAR', $args);
        return $this;
    }

    public function orWhereYear(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereLikeDate('YEAR', $args);
        return $this;
    }

    public function whereMonth(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereLikeDate('MONTH', $args);
        return $this;
    }

    public function orWhereMonth(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereLikeDate('MONTH', $args);
        return $this;
    }


    public function whereDay(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereLikeDate('DAY', $args);
        return $this;
    }

    public function orWhereDay(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereLikeDate('DAY', $args);
        return $this;
    }

    public function whereTime(...$args)
    {
        $this->addOperator('AND');
        $this->queryMakerWhereLikeDate('TIME', $args);
        return $this;
    }

    public function orWhereTime(...$args)
    {
        $this->addOperator('OR');
        $this->queryMakerWhereLikeDate('TIME', $args);
        return $this;
    }


    public function and(...$args)
    {
        return $this->where(...$args);
    }

    public function or(...$args)
    {
        return $this->orWhere(...$args);
    }

    public function not(...$args)
    {
        return $this->whereNot(...$args);
    }

    public function orNot(...$args)
    {
        return $this->orWhereNot(...$args);
    }

    public function like($column, $value)
    {
        return $this->where($column, 'like', $value);
    }

    public function orLike($column, $value)
    {
        return $this->orWhere($column, 'like', $value);
    }


    public function null($column)
    {
        return $this->whereNull($column);
    }

    public function orNull($column)
    {
        return $this->orWhereNull($column);
    }


    public function notNull($column)
    {
        return $this->whereNotNull($column);
    }

    public function orNotNull($column)
    {
        return $this->orWhereNotNull($column);
    }


    public function is($column, $boolean = true)
    {
        return $this->where($column, $boolean);
    }

    public function true($column)
    {
        return $this->is($column, false);
    }

    public function false($column)
    {
        return $this->is($column, false);
    }

    public function date(...$args)
    {
        return $this->whereDate(...$args);
    }

    public function orDate(...$args)
    {
        return $this->orWhereDate(...$args);
    }


    public function year(...$args)
    {
        return $this->whereYear(...$args);
    }

    public function orYear(...$args)
    {
        return $this->orWhereYear(...$args);
    }


    public function month(...$args)
    {
        return $this->whereMonth(...$args);
    }

    public function orMonth(...$args)
    {
        return $this->orWhereMonth(...$args);
    }


    public function day(...$args)
    {
        return $this->whereDay(...$args);
    }

    public function orDay(...$args)
    {
        return $this->orWhereDay(...$args);
    }


    public function time(...$args)
    {
        return $this->whereTime(...$args);
    }

    public function orTime(...$args)
    {
        return $this->orWhereTime(...$args);
    }


    public function in($name, array $list)
    {
        return $this->whereIn($name, $list);
    }

    public function notIn($name, array $list)
    {
        return $this->whereNotIn($name, $list);
    }

    public function orIn($name, array $list)
    {
        return $this->orWhereIn($name, $list);
    }

    public function orNotIn($name, array $list)
    {
        return $this->orwhereNotIn($name, $list);
    }


    public function whereRaw($query, array $values, $boolean = 'AND')
    {
        $this->addOperator($boolean);
        $this->addToSourceArray('WHERE', $this->makeRaw($query, $values));
        return $this;
    }

    public function orWhereRaw($query, array $values)
    {
        return $this->whereRaw($query, $values, 'OR');
    }

    public function whereIn($name, array $list)
    {
        $query = $this->queryMakerIn($name, $list, '');
        $this->addOperator('AND');
        $this->addToSourceArray('WHERE', $query);
        return $this;
    }

    public function whereNotIn($name, array $list)
    {
        $query = $this->queryMakerIn($name, $list, 'NOT');
        $this->addOperator('AND');
        $this->addToSourceArray('WHERE', $query);
        return $this;
    }

    public function orWhereIn($name, array $list)
    {
        $query = $this->queryMakerIn($name, $list, '');
        $this->addOperator('OR');
        $this->addToSourceArray('WHERE', $query);
        return $this;
    }

    public function orWhereNotIn($name, array $list)
    {
        $query = $this->queryMakerIn($name, $list, 'NOT');
        $this->addOperator('OR');
        $this->addToSourceArray('WHERE', $query);
        return $this;
    }


    public function whereColumn($first, $operator, $second = false)
    {

        $this->addOperator('AND');
        $this->fixOperatorAndValue($operator, $second);
        $this->addToSourceArray('WHERE', "`$first` $operator `$second`");

        return $this;
    }

    protected function addOperator($oprator)
    {
        $array = $this->getSourceValueItem('WHERE');

        if (count($array) > 0) {

            $end = $array[count($array) - 1];

            if (in_array($end, ['AND', 'OR', '(']) == false) {
                $this->addToSourceArray('WHERE', $oprator);
            }
        } else {
            $this->addToSourceArray('WHERE', 'WHERE');
        }
    }

    protected function addOperatorHaving($oprator)
    {
        $array = $this->getSourceValueItem('HAVING');

        if (count($array) > 0) {

            $end = $array[count($array) - 1];

            if (in_array($end, ['AND', 'OR', '(']) == false) {
                $this->addToSourceArray('HAVING', $oprator);
            }
        }
    }

    private function queryMakerWhere($args, $extra_operation = '')
    {
        if (is_string($args[0])) {

            $column = $args[0];
            $operator = $args[1];
            $value = $args[2] ?? false;

            $this->fixOperatorAndValue($operator, $value);

            $column = $this->fixColumnName($column)['name'];

            $value_name = $this->bindParamAutoName($value);

            $query = "$column $operator $value_name";

            if (!empty($extra_operation)) {
                $query = 'NOT ' . $query;
            }

            $this->addToSourceArray('WHERE', $query);
        } else if (is_callable($args[0])) {

            $this->addStartParentheses();
            $args[0]($this);
            $this->addEndParentheses();
        }
    }

    private function queryMakerWhereBetween($name, array $values, $extra_operation = '')
    {
        $name = $this->fixColumnName($name)['name'];

        $v1 = $this->bindParamAutoName($values[0]);
        $v2 = $this->bindParamAutoName($values[1]);

        $query = "$name BETWEEN $v1 AND $v2";

        if (!empty($extra_operation)) {
            $query = 'NOT ' . $query;
        }

        $this->addToSourceArray('WHERE', $query);
    }

    private function queryMakerWhereLikeDate($action, $args)
    {

        $column = $args[0];
        $operator = $args[1];
        $value = $args[2] ?? false;

        $this->fixOperatorAndValue($operator, $value);

        $column = $this->fixColumnName($column)['name'];

        $value_name = $this->bindParamAutoName($column);


        $query = "$action($column) $operator $value_name";

        $this->addToSourceArray('WHERE', $query);
    }

    private function queryMakerWhereStaticValue($name, $value)
    {
        $name = $this->fixColumnName($name)['name'];

        $query = "$name $value";

        if (!empty($extra_operation)) {
            $query = 'NOT ' . $query;
        }

        $this->addToSourceArray('WHERE', $query);
    }

    private function queryMakerJoin($type, $args)
    {
        $join_table = $args[0];
        $join_table_column = $args[1];
        $operator = $args[2] ?? false;
        $main_column = $args[3] ?? false;

        if (!$operator && !$main_column) {
            $table_second = $this->fixColumnName($join_table);
            $table_main = $this->fixColumnName($join_table_column);

            $join_table = $table_second['table'];

            $join_table_column = $table_second['name'];

            $operator = '=';

            $main_column = $table_main['name'];
        } else if ($operator && !$main_column) {
            $table_second = $this->fixColumnName($join_table);
            $table_main = $this->fixColumnName($operator);

            $operator = $join_table_column;

            $join_table = $table_second['table'];
            $join_table_column = $table_second['name'];

            $main_column = $table_main['name'];
        } else if ($main_column) {
            $join_table = "`$join_table`";

            $join_table_column = $this->fixColumnName($join_table_column)['name'];
            $main_column = $this->fixColumnName($main_column)['name'];
        }

        return "$type JOIN $join_table ON $join_table_column $operator $main_column";
    }

    private function queryMakerIn($name, array $list, $extra_opration = '')
    {

        if (count($list) == 0) {
            return '';
        }

        $name = $this->fixColumnName($name)['name'];

        $values = [];

        $this->methodInMaker($list, function ($get_param_name) use (&$values) {
            $values[] = $get_param_name;
        });

        $string_query_name = $name;

        if (!empty($extra_opration)) {
            $string_query_name .= ' ' . $extra_opration;
        }


        $string_query_value = 'IN(' . implode(',', $values) . ')';

        $string_query = "$string_query_name $string_query_value";

        return $string_query;
    }

    protected function addStartParentheses()
    {
        $this->addToSourceArray('WHERE', '(');
    }

    protected function addEndParentheses()
    {
        $this->addToSourceArray('WHERE', ')');
    }

    protected function execute($query, $binds = [], $return = false)
    {
        $this->binds = $binds;
        if ($this->binds == null) {
            $stmt = $this->conn->pdo()->query($query);
        } else {
            $stmt = $this->conn->pdo()->prepare($query);
            $stmt->execute($this->binds);
        }

        if ($return) {
            $result = $stmt->fetchAll($this->conn->getFetch());
        } else {
            $result = $stmt->rowCount();
        }


        return $result;
    }

    protected function makeSourceValueStrign()
    {
        ksort($this->sourceValue);

        $array = [];
        foreach ($this->sourceValue as $value) {
            if (is_array($value)) {
                $array[] = implode(' ', $value);
            }
        }

        return implode(' ', $array);
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return int
     */
    public function count($column = '*')
    {
        $this->select(function ($query) use ($column) {
            $query->countFn($column)->as('count');
        });
        return $this->get_value($this->first(), 'count');
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $columns
     * @return int
     */
    public function sum($column = '*')
    {
        $this->select(function ($query) use ($column) {
            $query->sumFn($column)->as('sum');
        });

        return $this->get_value($this->first(), 'sum');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function avg($column = '*')
    {
        $this->select(function ($query) use ($column) {
            $query->avgFn($column)->as('avg');
        });

        return $this->get_value($this->first(), 'avg');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function min($column = '*')
    {
        $this->select(function ($query) use ($column) {
            $query->minFn($column)->as('min');
        });

        return $this->get_value($this->first(), 'min');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function max($column = '*')
    {
        $this->select(function ($query) use ($column) {
            $query->maxFn($column)->as('max');
        });

        return $this->get_value($this->first(), 'max');
    }

    private function clone()
    {
        $db = clone $this;
        $db->binds = $this->binds;
        $db->sourceValue = $this->sourceValue;
        $db->clearSource('SELECT');
        $db->clearSource('LIMIT');
        $db->clearSource('OFFSET');
        $db->clearSource('FROM');
        return $db;
    }

    public function get()
    {
        $query = $this->makeSelectQueryString();
        return $this->execute($query, $this->binds, true);
    }
}
