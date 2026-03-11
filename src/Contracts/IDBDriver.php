<?php

namespace SophyDB\Contracts;

interface IDBDriver
{
    public function connect(): void;

    public function pdo(): \PDO;

    public function getFetch(): int;
}
