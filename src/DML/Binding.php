<?php

namespace SophyDB\DML;

trait Binding
{
    private $position = 0;

    protected function bindParamAutoName($value)
    {
        $name = $this->assignParamName();
        return $this->bindParam($name, $value);
    }

    protected function assignParamName()
    {
        $this->position++;
        return 'p' . $this->position;
    }

    protected function bindParam($name, $value)
    {
        if ($value === false) {
            $value = 0;
        } elseif ($value === true) {
            $value = 1;
        }

        $this->binds[":$name"] = $value;
        return ":$name";
    }

    protected function addToParamAutoName($value){
        $name = $this->assignParamName();
        return $this->bindParam($name, $value);
      } 
}
