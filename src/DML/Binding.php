<?php

namespace SophyDB\DML;

use SophyDB\SQLCommands\Keywords;

class Binding
{
    private $position = 0;

    private DML $dml;
    public Keywords $keyWords;

    public function __construct($dml = null)
    {
        $this->dml = $dml;
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

        $this->dml->binds[":$name"] = $value;
        return ":$name";
    }

    public function clearSource($struct_name)
    {
        $s_index = $this->keyWords->get($struct_name);
        $this->dml->sourceValue[$s_index] = [];
    }

    public function addToSourceArray($struct_name, $value)
    {
        $s_index = $this->keyWords->get($struct_name);
        $this->dml->sourceValue[$s_index][] = $value;
    }

    public function getSourceValueItem($struct_name)
    {
        $s_index = $this->keyWords->get($struct_name);
        return $this->dml->sourceValue[$s_index] ?? [];
    }

    public function addToParamAutoName($value)
    {
        $name = $this->assignParamName();
        return $this->bindParam($name, $value);
    }

    public function makeSourceValueString()
    {
        ksort($this->dml->sourceValue);

        $array = [];
        foreach ($this->dml->sourceValue as $value) {
            if (is_array($value)) {
                $array[] = implode(' ', $value);
            }
        }

        return implode(' ', $array);
    }
}
