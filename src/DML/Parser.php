<?php

namespace SophyDB\DML;

use SophyDB\SQLCommands\MySQL\Select;

class Parser
{
  private Select $select;

  public function __construct($select = null)
  {
    $this->select = $select;
  }
  public function methodInMaker(array $list, $callback)
  {
    foreach ($list as $item) {
      $param_name = $this->select->binding->addToParamAutoName($item);
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
      $table = $this->select->dml->table;
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

  public function fixOperatorAndValue(&$operator, &$value)
  {
    if ($value == false || $value == null) {
      $value = $operator;
      $operator = '=';
    }
  }
}
