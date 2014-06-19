<?php

namespace API\Lists;

class Verein extends \API\Lists\Lists{

  public function __construct($liga){
    if($liga){
      parent::__construct('SELECT Verein.id, Verein.name FROM Verein, Team WHERE Team.liga_id=:liga AND Team.verein_id = Verein.id');
      $this->execute(array('liga' => $liga));
    }else{
      parent::__construct('SELECT id, name FROM Verein');
      $this->execute();
    }
  }

  public function getList(){
    return $this->list;
  }
}

?>
