<?php

namespace SophyDB\Database;

use SophyDB\Contracts\Grammar;
use SophyDB\Contracts\IDBDriver;
use SophyDB\Grammar\MySQLGrammar;
use SophyDB\Grammar\PostgreSQLGrammar;
use SophyDB\Grammar\SQLiteGrammar;
use SophyDB\Grammar\SQLServerGrammar;

class PDODriver implements IDBDriver
{
    const FETCH_CLASS = \PDO::FETCH_CLASS;
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;
    const FETCH_OBJ   = \PDO::FETCH_OBJ;

    private $params;
    private $connection = null;
    private $grammar = null;

    public function __construct(array $params)
    {
        $this->params  = $params;
        $this->grammar = $this->resolveGrammar();
    }

    private function resolveGrammar(): Grammar
    {
        $driver = $this->params['driver'] ?? 'mysql';

        if ($driver === 'pgsql' || $driver === 'postgresql') {
            return new PostgreSQLGrammar();
        }
        if ($driver === 'sqlite') {
            return new SQLiteGrammar();
        }
        if ($driver === 'sqlsrv' || $driver === 'sqlserver') {
            return new SQLServerGrammar();
        }
        return new MySQLGrammar();
    }

    public function grammar(): Grammar
    {
        return $this->grammar;
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
