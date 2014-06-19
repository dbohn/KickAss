<?php

namespace API\Lists;

class Saison extends \API\Lists\Lists{

  public function __construct(){
    parent::__construct('SELECT start_datum, end_datum, liga, name FROM Saison, Liga WHERE Liga.id = Saison.liga');

    $this->execute();
  }

  public function getList(){
    foreach($this->list as $res){

      $start = new \DateTime($res['start_datum']);
      $end = new \DateTime($res['end_datum']);

      $id = $res['liga'].'.'.$res['start_datum'];
      $name = $res['name'].' Saison '.$start->format('y').'/'.$end->format('y');
      $out[] = array('id' => $id, 'name' => $name);

    }

    return $out;
  }
}

?>
