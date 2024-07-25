<?php

namespace SophyDB\SQLCommands\MySQL;

use SophyDB\DML\DML;

class Delete
{
    public DML $dml;

    public function __construct(DML $dml)
    {
        $this->dml = $dml;
    }

    public function delete()
    {
        $this->dml->setAction('delete');
        $query = $this->makeDeleteQueryString();
        return $this->dml->execute($query, $this->dml->binds);
    }

    protected function makeDeleteQueryString()
    {
        $table = $this->dml->table;
        $extra = $this->dml->binding->makeSourceValueString();
        return "DELETE FROM `$table`  $extra";
    }
}
