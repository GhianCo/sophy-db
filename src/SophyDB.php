<?php

namespace SophyDB;

use Sophy\Database\Drivers\IDBDriver;
use Sophy\Database\Drivers\PDODriver;
use SophyDB\DML\DML;
use SophyDB\SQLCommands\MySQL\Raw;

final class SophyDB
{
    public IDBDriver $database;

    protected static $CONN_DEFAULT = IDBDriver::class;

    public static function table($name)
    {
        $dml = new DML;
        $dml->setConnection(app(self::$CONN_DEFAULT));
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
        singleton($connName, function () use ($params) {
            return new PDODriver($params);
        });
    }

    private static function getCurrentConn()
    {
        return app(self::$CONN_DEFAULT);
    }

    public static function use(string $config_name)
    {
        self::$CONN_DEFAULT = $config_name;
        return new static;
    }

    public static function beginTransaction()
    {
        self::getCurrentConn()->connect();
        self::getCurrentConn()->pdo()->beginTransaction();
    }

    public static function rollBack()
    {
        self::getCurrentConn()->pdo()->rollBack();
    }

    public static function commit()
    {
        self::getCurrentConn()->pdo()->commit();
    }

    public static function query($sql, $params = [], $isList = false)
    {
        $dml = new DML;
        $dml->setConnection(app(self::$CONN_DEFAULT));
        return $dml->execute($sql, $params, true, $isList);
    }
}
