<?php

namespace API;

interface IQuery{
  public function exec($arr);
  public function getSQL();
}

?>
