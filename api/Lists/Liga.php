<?php

namespace API\Lists;

class Liga extends \API\Lists\Lists{

  public function __construct(){
    parent::__construct('SELECT id,name FROM Liga');
    $this->execute();
  }

  public function getList(){
    return $this->list;
  }
}

?>
