<?php

namespace SophyDB;

use SophyDB\Contracts\IDBDriver;
use SophyDB\Database\PDODriver;
use SophyDB\DML\DML;
use SophyDB\SQLCommands\MySQL\Raw;

final class SophyDB
{
    private static $connections = [];

    protected static $CONN_DEFAULT = IDBDriver::class;

    public static function table($name)
    {
        $dml = new DML;
        $dml->setConnection(self::getCurrentConn());
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
        self::$connections[$connName] = new PDODriver($params);
    }

    private static function getCurrentConn()
    {
        if (!isset(self::$connections[self::$CONN_DEFAULT])) {
            throw new \RuntimeException(
                "No hay conexión registrada con el nombre '" . self::$CONN_DEFAULT . "'."
            );
        }
        return self::$connections[self::$CONN_DEFAULT];
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
        $dml->setConnection(self::getCurrentConn());
        return $dml->execute($sql, $params, true, $isList);
    }
}
