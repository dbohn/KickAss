<?php

namespace API\Answer;

class Firstgame extends \API\Answer\Answer{

  public function __construct($identifier){
    parent::__construct('SELECT anpfiff_datum FROM Spiel, Saison, Liga WHERE Saison.liga = :liga AND Spiel.saison_id = Saison.id AND Saison.start_datum = :datum ORDER BY Spieltag, anpfiff_datum LIMIT 1 ');

    $match = preg_match_all('/(\d)\.(\d{4}\-\d{1,2}\-\d{1,2})/', $identifier, $matches);

    if(count($matches) != 3 || count($matches[1]) != 1 || count($matches[2]) != 1 || !is_numeric($matches[1][0])){
      throw new \Exception('Invalid identifier format.');
    }

    $datum = $matches[2][0];
    $liga = $matches[1][0];

    // check if date is valid
    $e = explode('-', $datum);
    if(!checkdate($e[1],$e[2], $e[0])){
      throw new \Exception('Invalid date.');
    }

    $this -> execute(array('datum' => $datum, 'liga' => $liga));
  }
}


?>
