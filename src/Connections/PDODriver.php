<?php

namespace SophyDB\Connections;

use PDO;

class PdoDriver implements IDBDriver
{
    protected ?PDO $pdo;

    const FETCH_CLASS = \PDO::FETCH_CLASS;
    const FETCH_ASSOC = \PDO::FETCH_ASSOC;

    public function connect(
        string $protocol,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password
    ) {

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
