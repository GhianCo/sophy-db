<?php

namespace SophyDB\DML;

use SophyDB\Connections\PdoConn;

trait Parser
{

  protected function methodInMaker(array $list, $callback)
  {
    foreach ($list as $item) {
      $param_name = $this->addToParamAutoName($item);
      $callback($param_name);
    }
  }
  public function fixColumnName($name)
  {
    $array = explode('.', $name);
    $count = count($array);

    $table = '';
    $column = '';
    $type = '';

    if ($count == 1) {
      $table = $this->table;
      $column = $array[0];
      $type = 'column';
    } else if ($count == 2) {
      $table = $array[0];
      $column = $array[1];
      $type = 'table_and_column';
    }

    if ($column != '*') {
      $column = "`$column`";
    }

    $table = "`$table`";

    return ['name' => "$table.$column", 'table' => $table, 'column' => $column, 'type' => $type];
  }

  protected function fixOperatorAndValue(&$operator, &$value)
  {
    if ($value == false || $value == null) {
      $value = $operator;
      $operator = '=';
    }
  }

  public function get_value($param, $name)
  {
    if ($this->conn->getFetch() == PdoConn::FETCH_CLASS) {
      return $param->{$name};
    } else {
      return $param[$name];
    }
  }
}
