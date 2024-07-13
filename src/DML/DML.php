<?php

namespace SophyDB\DML;

use SophyDB\Connections\PdoDriver;
use SophyDB\SophyDB;
use SophyDB\SQLCommands\MySQL\Select;
use stdClass;

final class DML
{
    public Select $select;

    public $sourceValue = [];
    public $conn;
    public $table;
    public $binds = [];

    public function __construct($select = null)
    {
        $this->select = $select ?: new Select($this);
    }

    public function setConnection($conn)
    {
        $this->conn = $conn;
    }

    public function setTable($table)
    {
        $this->table = $table;
    }

    public function value($column = '*')
    {
        return $this->getValue($this->first(), $column);
    }

    public function getValue($param, $name)
    {
        if ($this->conn->getFetch() == PdoDriver::FETCH_CLASS) {
            return $param->{$name};
        } else {
            return $param[$name];
        }
    }

    public function execute($query, $binds = [], $return = false)
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
    
    public function get()
    {
        $query = $this->select->makeSelectQueryString();
        return $this->execute($query, $this->binds, true);
    }

    public function clone()
    {
        $db = SophyDB::table($this->table);
        $db->binds = $this->binds;
        $db->sourceValue = $this->sourceValue;
        $db->select->binding->clearSource('SELECT');
        $db->select->binding->clearSource('LIMIT');
        $db->select->binding->clearSource('OFFSET');
        $db->select->binding->clearSource('FROM');
        return $db;
    }

    public function __call($name, $arguments) {
        if (method_exists($this->select, $name)) {
            $result = call_user_func_array([$this->select, $name], $arguments);
            if (!is_object($result)|| $result instanceof stdClass || $result instanceof self) {
                return $result;
            }
            return $this;
        }
        if (method_exists($this->select->where, $name)) {
            $result = call_user_func_array([$this->select->where, $name], $arguments);
            if (!is_object($result)|| $result instanceof stdClass || $result instanceof self) {
                return $result;
            }
            return $this;
        }

        if (method_exists($this->select->join, $name)) {
            call_user_func_array([$this->select->join, $name], $arguments);
            return $this;
        }

        if (method_exists($this->select->having, $name)) {
            call_user_func_array([$this->select->having, $name], $arguments);
            return $this;
        }

        if (method_exists($this->select->orderBy, $name)) {
            call_user_func_array([$this->select->orderBy, $name], $arguments);
            return $this;
        }
    }
}
