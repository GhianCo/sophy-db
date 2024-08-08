<?php

namespace SophyDB;

use Sophy\Database\Drivers\IDBDriver;
use Sophy\Database\Drivers\PDODriver;
use SophyDB\DML\DML;
use SophyDB\SQLCommands\MySQL\Raw;

final class SophyDB
{
    public IDBDriver $database;

    protected static $DB_DEFAULT = IDBDriver::class;

    public static function table($name)
    {
        $dml = new DML();
        $dml->setConnection(app(self::$DB_DEFAULT));
        $dml->setTable($name);
        return $dml;
    }

    public static function colsRaw($query, array $values = [])
    {
        $raw = new Raw;
        $raw->setRawData($query, $values);
        return $raw;
    }

    public static function addConn(array $params, $connName = IDBDriver::class)
    {
        singleton($connName, function() use($params) {
            return new PDODriver($params);
        });
    }

    public static function use(string $config_name)
    {
        self::$DB_DEFAULT = $config_name;
        return new static;
    }
}
