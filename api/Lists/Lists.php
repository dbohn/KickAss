<?php

namespace API\Lists;

abstract class Lists implements \API\Lists\IList{

  private $query;
  protected $list;

  public function __construct($sqlstring){
    $this->query = new \API\PGQuery($sqlstring);
  }

  protected function execute($params = array()){
    $this->list = $this->query->exec($params);
  }

  abstract public function getList();

}

?>
