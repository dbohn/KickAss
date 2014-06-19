<?php

namespace API\Answer;

abstract class Answer implements IAnswer{

  private $query;
  private $data;

  public function __construct($sqlstring){
    $this->query = new \API\PGQuery($sqlstring);
  }

  protected function execute($params = array()){
    $this->data = $this->query->exec($params);
  }

  public function getData(){
    return $this->data;
  }

  public function getSQL(){
    return $this->query->getSQL();
  }

}

?>
