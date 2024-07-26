<?php

namespace SophyDB;

class Model
{
    protected $table;

    protected $primaryKey = 'id';

    protected array $fillable = [];

    protected array $attributes = [];

    protected function makeInstance($name, $arguments = [])
    {
        $db = SophyDB::table($this->table);
        $db->setPrimaryKey($this->primaryKey);
        $db = $db->{$name}(...$arguments);
        return $db;
    }

    public static function __callStatic($name, $arguments)
    {
        return (new static)->makeInstance($name, $arguments);
    }


    public function __call($name, $arguments)
    {
        return $this->makeInstance($name, $arguments);
    }
}
