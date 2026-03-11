<?php

namespace SophyDB\Database;

use SophyDB\Contracts\IDBDriver;

class PDODriver implements IDBDriver
{
    const FETCH_CLASS = \PDO::FETCH_CLASS;
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;
    const FETCH_OBJ   = \PDO::FETCH_OBJ;

    private $params;
    private $connection = null;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function connect(): void
    {
        if ($this->connection !== null) {
            return;
        }

        $driver   = $this->params['driver']   ?? 'mysql';
        $host     = $this->params['host']     ?? 'localhost';
        $port     = $this->params['port']     ?? '';
        $database = $this->params['database'] ?? '';
        $charset  = $this->params['charset']  ?? DSN::UTF8;
        $username = $this->params['username'] ?? 'root';
        $password = $this->params['password'] ?? '';

        $dsn = "{$driver}:host={$host}";

        if ($port !== false && !empty($port)) {
            $dsn .= ":{$port}";
        }

        $dsn .= ";dbname={$database};";

        if (strtolower($driver) === 'sqlsrv') {
            $dsn = str_replace('dbname', 'Database', str_replace(':host', ':Server', $dsn));
        } else {
            $dsn .= "charset={$charset};";
        }

        $this->connection = new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => $this->params['fetch'] ?? \PDO::FETCH_CLASS,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    public function pdo(): \PDO
    {
        return $this->connection;
    }

    public function getFetch(): int
    {
        return $this->params['fetch'] ?? \PDO::FETCH_CLASS;
    }
}
