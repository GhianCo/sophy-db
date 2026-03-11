<?php

namespace SophyDB\Contracts;

interface Grammar
{
    public function quoteIdentifier(string $name): string;

    public function randomFunction(): string;
}
