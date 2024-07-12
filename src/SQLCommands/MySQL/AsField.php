<?php

namespace SophyDB\SQLCommands\MySQL;

class AsField
{
    protected $select;

    public function __construct($select)
    {
        $this->select = $select;
    }

    public function as($value)
    {
        $end_index = count($this->select->stringArray) - 1;
        $this->select->stringArray[$end_index] = $this->select->stringArray[$end_index] . " as '$value'";
    }
}
