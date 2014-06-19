<?php

namespace API\Answer;

class Won extends \API\Answer\Answer{

  public function __construct($teamid){
    parent::__construct('SELECT count(*) AS gewonnen  FROM Spiel WHERE (gastgeber_id = :teamid AND toreHeim > toreGast) OR (gast_id = :teamid AND toreGast > toreHeim)');

    $this -> execute(array('teamid' => $teamid));
  }
}


?>
