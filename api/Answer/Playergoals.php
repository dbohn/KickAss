<?php

namespace API\Answer;

class Playergoals extends \API\Answer\Answer{

  public function __construct($goals){
    parent::__construct('SELECT name, anzahl FROM Spieler, erzielt_tore WHERE anzahl > :goals AND Spieler.id = erzielt_tore.spieler_id ORDER BY anzahl DESC');

    $this -> execute(array('goals' => $goals));
  }
}


?>
