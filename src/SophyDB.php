<?php

namespace SophyDB;

use SophyDB\Connections\DSN;
use PDO;
use SophyDB\Connections\IDriver;
use SophyDB\DML\DML;
use SophyDB\SQLCommands\MySQL\Raw;

final class SophyDB
{
    public IDriver $database;

    private $hasConnection = false;

    public const FETCH_CLASS = PDO::FETCH_CLASS;
    public const FETCH_ASSOC = PDO::FETCH_ASSOC;
    private $fetch = '';

    protected static $DB_DEFAULT = 'main';
    protected static $connections = [];

    private $pdo = null;

    /**
     * The Constructor
     */

    private function __construct($pdo, $connName, $fetch)
    {
        if (!$this->hasConnection) {
            $this->pdo = $pdo;
            $this->hasConnection = true;
            $this->fetch = $fetch;
            self::$connections[$connName] = $this;
        }
    }

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

    public static function raw($query, array $values = [])
    {
        $raw = new Raw;
        $raw->setRawData($query, $values);
        return $raw;
    }


    public function pdo()
    {
        if (!$this->pdo) {
            throw new \Exception("The database settings were not made correctly and the connection was not established.");
        }

        return $this->pdo;
    }

    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     * Get a new instance
     */
    public static function factory(array $params, $connName = 'main')
    {
        $dsn = DSN::factory($params['database'] ?? 'SophyDB');

        $username = $params['username'] ?? 'root';
        $password = $params['password'] ?? '';
        $charset = $params['charset'] ?? DSN::UTF8;
        $collation = $params['collation'] ?? DSN::UTF8_GENERAL_CI;
        $fetch = $params['fetch'] ?? self::FETCH_CLASS;

        $pdo = new \PDO((string)$dsn(), $username, $password, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '$charset' COLLATE '$collation'"
        ]);

        return new self($pdo, $connName, $fetch);
    }
}
