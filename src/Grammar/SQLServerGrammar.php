<?php

namespace SophyDB\Grammar;

use SophyDB\Contracts\Grammar;

class SQLServerGrammar implements Grammar
{
    public function quoteIdentifier(string $name): string
    {
        return "[{$name}]";
    }

    public function randomFunction(): string
    {
        return 'NEWID()';
    }
}
