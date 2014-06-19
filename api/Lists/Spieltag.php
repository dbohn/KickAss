<?php

namespace API\Lists;

class Spieltag extends \API\Lists\Lists{

  public function __construct($liga){
    if($liga){
      parent::__construct('SELECT DISTINCT spieltag FROM Spiel WHERE liga_id = :liga ORDER BY spieltag');
      $this->execute(array('liga' => $liga));
    }else{
      parent::__construct('SELECT DISTINCT spieltag FROM Spiel ORDER BY spieltag');
      $this->execute();
    }
  }

  public function getList(){
    foreach($this->list as $res){

      $name = sprintf('%2d. Spieltag', $res['spieltag']);
      $out[] = array('id' => $res['spieltag'], 'name' => $name);
    }

    return $out;
  }
}

?>
