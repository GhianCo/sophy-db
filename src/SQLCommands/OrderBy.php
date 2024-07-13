<?php

namespace SophyDB\SQLCommands;

use SophyDB\SQLCommands\MySQL\Select;

final class OrderBy
{
    private Select $select;

    public function __construct($select = null)
    {
        $this->select = $select;
    }

    public function orderBy($columns, $direction = 'asc')
    {
        $column_string = '';

        if (is_array($columns)) {
            $array_string = [];

            foreach ($columns as $column) {

                if (is_array($column) && count($column) == 2) {
                    $array_string[] = $this->select->parser->fixColumnName($column[0])['name'] . " " . $column[1];
                } else {
                    $array_string[] = $this->select->parser->fixColumnName($column)['name'] . " $direction";
                }
            }

            $column_string = implode(',', $array_string);
            $this->select->binding->addToSourceArray('ORDER_BY', "ORDER BY $column_string");
        } else {
            $column_string = $this->select->parser->fixColumnName($columns)['name'];
            $this->select->binding->addToSourceArray('ORDER_BY', "ORDER BY $column_string $direction");
        }

        return $this;
    }
    public function orderByCount($column, $direction = 'asc')
    {
        $column = $this->select->parser->fixColumnName($column)['name'];
        $this->select->binding->addToSourceArray('ORDER_BY', "ORDER BY COUNT($column) $direction");
        return $this;
    }

    public function inRandomOrder()
    {
        $this->select->binding->addToSourceArray('ORDER_BY', "ORDER BY RAND()");
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
