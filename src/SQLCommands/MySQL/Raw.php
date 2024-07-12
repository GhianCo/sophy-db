<?php

namespace SophyDB\SQLCommands\MySQL;

final class Raw
{
  protected $binds = [];
  protected $query;

  public function setRawData($query, array $binds)
  {
    $this->query = $query;
    $this->binds = $binds;
  }

  public function getRawQuery()
  {
    return $this->query;
  }

  public function getRawValues()
  {
    return $this->query;
  }
}
