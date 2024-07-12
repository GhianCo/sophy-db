<?php

namespace SophyDB\Connections;

use PDO;

class PdoConn implements IDriver
{
    protected ?PDO $pdo;

    const FETCH_CLASS = \PDO::FETCH_CLASS;
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;

    private $IS_CONNECT = false;

    private $DRIVER = '';
    private $SERVER_HOST = '';
    private $SERVER_PORT = '';

    private $DATABASE_NAME = '';
    private $USERNAME = '';
    private $PASSWORD = '';
    private $FETCH = '';
    private $CHARSET = 'utf8mb4';
    private $COLLATION = '';

    public function connect(
        string $protocol,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password
    ) {
        $this->pdo = new PDO('', $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES '$this->CHARSET' COLLATE '$this->COLLATION'");

        //if (DB::$CHANGE_ONCE) {
        //    DB::$CHANGE_ONCE = false;
        //    DB::$USE_DATABASE = 'main';
        //}

        $this->IS_CONNECT = true;
    }

    /**
     * Returns the PDO object for the database connection.
     *
     * @return \PDO The PDO object for the database connection.
     *
     * @throws Exception if database settings were not made correctly and the connection was not established.
     */
    public function pdo()
    {
        if (!$this->pdo) {
            throw new \Exception("Could not establish a connection to the database. Please check your database configuration settings in config file or ensure that your database server is running");
        }

        return $this->pdo;
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    public function statement(string $query, array $bind = []): mixed
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
