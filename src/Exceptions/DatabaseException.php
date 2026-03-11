<?php

namespace SophyDB\Exceptions;

class DatabaseException extends \RuntimeException
{
    public static function showMessage(string $message): self
    {
        return new self($message);
    }
}
