<?php

namespace SophyDB\DML;

use SophyDB\SQLCommands\Keywords;
use SophyDB\SQLCommands\MySQL\Select;

class Binding
{
    private $position = 0;

    private Select $select;
    public Keywords $keyWords;
    
    public function __construct($select = null)
    {
        $this->select = $select;
        $this->keyWords = new Keywords();
    }
    
    public function bindParamAutoName($value)
    {
        $name = $this->assignParamName();
        return $this->bindParam($name, $value);
    }

    public function assignParamName()
    {
        $this->position++;
        return 'p' . $this->position;
    }

    public function bindParam($name, $value)
    {
        if ($value === false) {
            $value = 0;
        } elseif ($value === true) {
            $value = 1;
        }

        $this->select->dml->binds[":$name"] = $value;
        return ":$name";
    }

    public function clearSource($struct_name)
    {
        $s_index = $this->keyWords->get($struct_name);
        $this->select->dml->sourceValue[$s_index] = [];
    }

    public function addToSourceArray($struct_name, $value)
    {
        $s_index = $this->keyWords->get($struct_name);
        $this->select->dml->sourceValue[$s_index][] = $value;
    }

    public function getSourceValueItem($struct_name)
    {
        $s_index = $this->keyWords->get($struct_name);
        return $this->select->dml->sourceValue[$s_index] ?? [];
    }

    public function addToParamAutoName($value){
        $name = $this->assignParamName();
        return $this->bindParam($name, $value);
    }
}
