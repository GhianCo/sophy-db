<?php

namespace SophyDB;

use Sophy\Exceptions\ConexionDBException;

class Model
{
    protected $table;

    protected $primaryKey = 'id';

    protected $fillable = [];

    protected $attributes = [];

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

    public static function create($attributes)
    {
        return (new static())->massAsign($attributes);
    }

    public static function update($attributes, int $id)
    {
        return (new static())->massAsign($attributes, $id);
    }

    protected function massAsign($attributes, $id = null)
    {
        if (count($this->fillable) == 0) {
            throw new \Error("Entidad " . static::class . " no tiene atributos por asignar");
        }

        if (isset($id)) {
            $this->attributes[$this->primaryKey] = $id;
        }

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->attributes[$key] = $value;
            }
        }

        return $this;
    }

    public function save()
    {
        try {
            if (isset($this->attributes[$this->primaryKey])) {
                $id = $this->attributes[$this->primaryKey];
                $this->where($this->primaryKey, $id)->update($this->attributes, $id);
            } else {
                $id = $this->insertGetId($this->attributes);
                $this->attributes[$this->primaryKey] = $id;
            }
            return $this->attributes;
        } catch (\Exception $exception) {
            throw ConexionDBException::showMessage($exception->getMessage());
        }
    }
}
