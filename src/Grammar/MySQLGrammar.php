<?php

namespace SophyDB\Grammar;

use SophyDB\Contracts\Grammar;

class MySQLGrammar implements Grammar
{
    public function quoteIdentifier(string $name): string
    {
        return "`{$name}`";
    }

    public function randomFunction(): string
    {
        return 'RAND()';
    }
}
