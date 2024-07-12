<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\Binding;
use SophyDB\DML\Parser;
use SophyDB\SophyDB;
use stdClass;

trait Select
{
    use Parser;
    use AggregateFn;
    use Binding;

    public $stringArray = [];

    protected static $binds = [];
    protected $table;
    protected $pk = 'id';

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function select(...$args)
    {
        $this->clearSource('DISTINCT');

        if (count($args) == 1 && !is_string($args[0]) && !$args[0] instanceof Raw) {
            if (is_array($args[0])) {
                foreach ($args[0] as $key => $arg) {
                    $args[$key] = $this->fixColumnName($arg)['name'];
                }

                $this->addToSourceArray('DISTINCT', implode(',', $args));
            } elseif (is_callable($args[0])) {
                $args[0]($this);
                $this->addToSourceArray('DISTINCT', $this->getString());
            }
        } else {
            foreach ($args as $key => $arg) {
                if ($arg instanceof Raw) {
                    $args[$key] = $this->makeRaw($arg->getRawQuery(), $arg->getRawValues());
                } else {
                    $args[$key] = $this->fixColumnName($arg)['name'];
                }
            }

            $this->addToSourceArray('DISTINCT', implode(',', $args));
        }
        return $this;
    }


    /**
     * Get a single column's value from the first result of a query.
     *
     * @param  string  $column
     * @return mixed
     */
    public function value($column = '*')
    {
        return $this->get_value($this->first(), $column);
    }

    public function first($columns = [])
    {
        $db = $this->limit(1);

        if (count($columns)) {
            $db->select($columns);
        }

        $array = $db->get();

        if (count($array) == 1) {
            return $array[0];
        }

        return false;
    }

    public function orderByCount($column, $direction = 'asc')
    {
        $column = $this->fix_column_name($column)['name'];
        $this->addToSourceArray('ORDER_BY', "ORDER BY COUNT($column) $direction");
        return $this;
    }


    public function inRandomOrder()
    {
        $this->addToSourceArray('ORDER_BY', "ORDER BY RAND()");
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

    public function limit(int $value)
    {
        $this->addToSourceArray('LIMIT', "LIMIT $value");
        return $this;
    }

    public function find($id, $columns = [])
    {
        return $this->where($this->pk, $id)->first($columns);
    }

    public function pluck($column, $key = null)
    {
        $list = $this->get();
        $result = [];
        foreach ($list as $item) {

            if ($key == null) {
                $result[] = $this->get_value($item, $column);
            } else {
                $result[$this->get_value($item, $key)] = $this->get_value($item, $column);
            }
        }

        return $result;
    }

    public function orderBy($columns, $direction = 'asc')
    {
        $column_string = '';

        if (is_array($columns)) {
            $array_string = [];

            foreach ($columns as $column) {

                if (is_array($column) && count($column) == 2) {
                    $array_string[] = $this->fixColumnName($column[0])['name'] . " " . $column[1];
                } else {
                    $array_string[] = $this->fixColumnName($column)['name'] . " $direction";
                }
            }

            $column_string = implode(',', $array_string);
            $this->addToSourceArray('ORDER_BY', "ORDER BY $column_string");
        } else {
            $column_string = $this->fixColumnName($columns)['name'];
            $this->addToSourceArray('ORDER_BY', "ORDER BY $column_string $direction");
        }

        return $this;
    }

    public function chunk($count, callable $callback)
    {
        $list = $this->get();

        do {
            $return = $callback(array_splice($list, 0, $count));
            if ($return === false) {
                break;
            }
        } while (count($list));
    }

    public function each(callable $callback)
    {
        $list = $this->get();

        do {
            $callback(array_splice($list, 0, 1)[0]);
        } while (count($list));
    }

    public function is($column, $boolean = true)
    {
        return $this->where($column, $boolean);
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists()
    {
        $result = $this->first();
        return $result ? true : false;
    }

    /**
     * Determine if no rows exist for the current query.
     *
     * @return bool
     */
    public function doesntExist()
    {
        return !$this->exists();
    }

    public function paginate(int $take = 15, int $page_number = null)
    {
        if ($page_number <= 0) {
            $page_number = 1;
        }

        $list = $this->page($page_number - 1, $take);
        $count = $this->clone()->count();

        $params = new stdClass;
        $params->last_page = ceil($count / $take);

        $nextpage = (($page_number) < $params->last_page) ? ($page_number + 1) : false;

        $prevpage = false;
        if ($page_number <= $params->last_page && $page_number > 1) {
            $prevpage = $page_number - 1;
        }

        $params->total = $count;
        $params->count = count($list);
        $params->per_page = $take;
        $params->prev_page = $prevpage;
        $params->next_page = $nextpage;
        $params->current_page = $page_number;
        $params->data = $list;

        return $params;
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int  $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function take(int $value)
    {
        return $this->limit($value);
    }

    /**
     * Set the "offset" value of the query.
     *
     * @param  int  $value
     * @return $this
     */
    public function offset(int $offset)
    {
        $this->addToSourceArray('OFFSET', "OFFSET $offset");
        return $this;
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param  int  $value
     * @return \Illuminate\Database\Query\Builder|static
     */
    public function skip(int $skip)
    {
        return $this->offset($skip);
    }

    public function page(int $page_number, int $take)
    {
        $offset = $page_number * $take;
        return $this->take($take)->offset($offset)->get();
    }

    /**
     * Add a "group by" clause to the query.
     *
     * @param  array  ...$groups
     * @return $this
     */
    public function groupBy(...$groups)
    {
        $arr = [];
        foreach ($groups as $group) {
            $arr[] = $this->fixColumnName($group)['name'];
        }
        $this->addToSourceArray('GROUP_BY', "GROUP BY " . implode(',', $arr));
        return $this;
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
        $this->fixOperatorAndValue($operator, $value);
        $column = $this->fixColumnName($column)['name'];

        $array = $this->getSourceValueItem('HAVING');
        $beginning = 'HAVING';

        if (count($array) > 0) {
            $beginning = '';
        }

        if (empty($fn)) {
            $this->addToSourceArray('HAVING', "$beginning $column $operator $value");
        } else {
            $this->addToSourceArray('HAVING', "$beginning $fn($column) $operator $value");
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

        $array = $this->getSourceValueItem('HAVING');
        $beginning = 'HAVING';

        if (count($array) > 0) {
            $beginning = '';
        }
        $raw = SophyDB::raw($sql, $bindings);
        $raw = $this->makeRaw($raw->getRawQuery(), $raw->getRawValues());
        $this->addToSourceArray('HAVING', "$beginning " . $raw);

        return $this;
    }

    public function orHavingRaw($sql, array $bindings = [])
    {
        return $this->havingRaw($sql, $bindings, 'OR');
    }

    function makeRaw($query, $values)
    {
        $index = 0;

        do {

            $find = strpos($query, '?');

            if ($find === false) {
                break;
            }

            $param_name = $this->bindParamAutoName($values[$index]);
            $query = substr_replace($query, $param_name, $find, 1);
            $index++;
        } while ($find !== false);

        return $query;
    }


    public function join(...$args)
    {
        $query = $this->queryMakerJoin('INNER', $args);
        $this->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function leftJoin(...$args)
    {
        $query = $this->queryMakerJoin('LEFT', $args);
        $this->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function rightJoin(...$args)
    {
        $query = $this->queryMakerJoin('RIGHT', $args);
        $this->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function fullJoin(...$args)
    {
        $query = $this->queryMakerJoin('FULL', $args);
        $this->addToSourceArray('JOIN', $query);
        return $this;
    }

    public function crossJoin($column)
    {
        $this->addToSourceArray('JOIN', "CROSS JOIN `$column`");
        return $this;
    }


    protected function makeSelectQueryString()
    {
        $this->addToSourceArray('SELECT', "SELECT");
        $this->addToSourceArray('FROM', "FROM `$this->table`");

        if (count($this->getSourceValueItem('DISTINCT')) == 0) {
            $this->select('*');
        }
        return $this->makeSourceValueStrign();
    }

    public function getString()
    {
        return implode(',', $this->stringArray);
    }
}
