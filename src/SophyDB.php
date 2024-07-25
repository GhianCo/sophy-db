<?php

namespace SophyDB;

use PDO;
use SophyDB\Connections\IDBDriver;
use SophyDB\Connections\PDODriver;
use SophyDB\DML\DML;
use SophyDB\SQLCommands\MySQL\Raw;

final class SophyDB
{
    public IDBDriver $database;

    protected static $DB_DEFAULT = 'main';
    protected static $connections = [];

    private static function currentConn()
    {
        return self::$connections[self::$DB_DEFAULT];
    }

    public static function table($name)
    {
        $dml = new DML();
        $dml->setConnection(self::currentConn());
        $dml->setTable($name);
        return $dml;
    }

    public static function colsRaw($query, array $values = [])
    {
        $raw = new Raw;
        $raw->setRawData($query, $values);
        return $raw;
    }

    public static function addConn(array $params, $connName = 'main')
    {
        self::$connections[$connName] = new PDODriver($params);
    }

    public static function use(string $config_name)
    {
        self::$DB_DEFAULT = $config_name;
        return new static;
    }
}
