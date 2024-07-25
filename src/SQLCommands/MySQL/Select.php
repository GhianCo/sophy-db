<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\DML;
use SophyDB\SQLCommands\Having;
use SophyDB\SQLCommands\Join;
use SophyDB\SQLCommands\OrderBy;
use SophyDB\SQLCommands\Where;
use stdClass;

class Select
{
    public $stringArray = [];

    public DML $dml;
    public AggregateFn $aggregateFn;
    public Join $join;
    public Where $where;
    public Having $having;
    public OrderBy $orderBy;


    public function __construct(DML $dml)
    {
        $this->dml = $dml;
        $this->aggregateFn = new AggregateFn($dml);
        $this->join = new Join($dml);
        $this->where = new Where($dml);
        $this->having = new Having($dml);
        $this->orderBy = new OrderBy($dml);
    }

    public function setTable($table)
    {
        $this->dml->table = $table;
    }

    public function cols(...$cols)
    {
        $this->dml->binding->clearSource('DISTINCT');

        if (count($cols) == 1 && !is_string($cols[0]) && !$cols[0] instanceof Raw) {
            if (is_array($cols[0])) {
                foreach ($cols[0] as $key => $arg) {
                    $cols[$key] = $this->dml->parser->fixColumnName($arg)['name'];
                }

                $this->dml->binding->addToSourceArray('DISTINCT', implode(',', $cols));
            } elseif (is_callable($cols[0])) {
                $aggregateFn = new AggregateFn($this->dml);
                $cols[0]($aggregateFn);
                $this->dml->binding->addToSourceArray('DISTINCT', $this->getString());
            }
        } else {
            foreach ($cols as $key => $arg) {
                if ($arg instanceof Raw) {
                    $cols[$key] = $this->makeRaw($arg->getRawQuery(), $arg->getRawValues());
                } else {
                    $cols[$key] = $this->dml->parser->fixColumnName($arg)['name'];
                }
            }

            $this->dml->binding->addToSourceArray('DISTINCT', implode(',', $cols));
        }
        return $this;
    }

    /**
     * Retrieve the "count" result of the query.
     *
     * @param  string  $columns
     * @return mixed
     */
    public function count($column = '*')
    {
        $this->cols(function ($query) use ($column) {
            $query->count($column)->as('count');
        });

        return $this->dml->getValue($this->first(), 'count');
    }

    /**
     * Retrieve the sum of the values of a given column.
     *
     * @param  string  $columns
     * @return int
     */
    public function sum($column = '*')
    {
        $this->cols(function ($query) use ($column) {
            $query->sum($column)->as('sum');
        });

        return $this->dml->getValue($this->first(), 'sum');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function avg($column = '*')
    {
        $this->cols(function ($query) use ($column) {
            $query->avg($column)->as('avg');
        });

        return $this->dml->getValue($this->first(), 'avg');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function min($column = '*')
    {
        $this->cols(function ($query) use ($column) {
            $query->min($column)->as('min');
        });

        return $this->dml->getValue($this->first(), 'min');
    }

    /**
     * Retrieve the average of the values of a given column.
     *
     * @param  string  $column
     * @return mixed
     */
    public function max($column = '*')
    {
        $this->cols(function ($query) use ($column) {
            $query->max($column)->as('max');
        });

        return $this->dml->getValue($this->first(), 'max');
    }

    public function colsRaw($query, array $values = [])
    {
        $raw = new Raw;
        $raw->setRawData($query, $values);
        $this->cols($raw);
        return $this;
    }

    public function limit(int $value)
    {
        $this->dml->binding->addToSourceArray('LIMIT', "LIMIT $value");
        return $this;
    }

    public function first($cols = [])
    {
        $db = $this->limit(1);

        if (count($cols)) {
            $db->cols($cols);
        }

        $array = $db->dml->get();

        if (count($array) == 1) {
            return $array[0];
        }

        return false;
    }

    public function chunk($count, callable $callback)
    {
        $list = $this->dml->get();

        do {
            $return = $callback(array_splice($list, 0, $count));
            if ($return === false) {
                break;
            }
        } while (count($list));
    }

    public function each(callable $callback)
    {
        $list = $this->dml->get();

        do {
            $callback(array_splice($list, 0, 1)[0]);
        } while (count($list));
    }

    public function pluck($column, $key = null)
    {
        $list = $this->dml->get();
        $result = [];
        foreach ($list as $item) {

            if ($key == null) {
                $result[] = $this->dml->getValue($item, $column);
            } else {
                $result[$this->dml->getValue($item, $key)] = $this->dml->getValue($item, $column);
            }
        }

        return $result;
    }

    public function paginate(int $take = 15, int $page_number = null)
    {
        if ($page_number <= 0) {
            $page_number = 1;
        }

        $list = $this->page($page_number - 1, $take);
        $count = $this->dml->clone()->count();

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
        $this->dml->binding->addToSourceArray('OFFSET', "OFFSET $offset");
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
        return $this->take($take)->offset($offset)->dml->get();
    }

    public function raw($query, array $values = [])
    {
        $raw = new Raw;
        $raw->setRawData($query, $values);
        return $raw;
    }

    public function makeRaw($query, $values)
    {
        $index = 0;

        do {

            $find = strpos($query, '?');

            if ($find === false) {
                break;
            }

            $param_name = $this->dml->binding->bindParamAutoName($values[$index]);
            $query = substr_replace($query, $param_name, $find, 1);
            $index++;
        } while ($find !== false);

        return $query;
    }

    public function makeSelectQueryString()
    {
        $table = $this->dml->table;
        $this->dml->binding->addToSourceArray('SELECT', "SELECT");
        $this->dml->binding->addToSourceArray('FROM', "FROM `$table`");

        if (count($this->dml->binding->getSourceValueItem('DISTINCT')) == 0) {
            $this->cols('*');
        }
        return $this->dml->binding->makeSourceValueString();
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
            $arr[] = $this->dml->parser->fixColumnName($group)['name'];
        }
        $this->dml->binding->addToSourceArray('GROUP_BY', "GROUP BY " . implode(',', $arr));
        return $this;
    }


    public function getString()
    {
        return implode(',', $this->stringArray);
    }
}
