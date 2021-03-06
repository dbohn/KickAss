<?php

/**
 * Liste aller Spieltage
 *
 * Es wird entweder das Maximum der Spieltage zurückgegeben (die
 * unterschiedlichen Ligen dauern unterschiedlich lange) oder bei Angabe einer Liga
 * werden die Spieltage für die entsprechende Liga aufgelistet.
 *
 * @author Luca Keidel
 */

namespace API\Lists;

/**
 * Liste aller Spieltage
 */
class Spieltag extends \API\Lists\Lists{

  /**
   * Konstruktor, führt die Abfrage durch
   *
   * @param int liga ID der Liga
   */
  public function __construct($liga){
    if($liga){
      parent::__construct('SELECT DISTINCT spieltag FROM Spiel WHERE liga_id = :liga ORDER BY spieltag');
      $this->execute(array('liga' => $liga));
    }else{
      parent::__construct('SELECT DISTINCT spieltag FROM Spiel ORDER BY spieltag');
      $this->execute();
    }
  }

  /**
   * Getter für die Liste
   */
  public function getList(){
    foreach($this->list as $res){

      $name = sprintf('%2d. Spieltag', $res['spieltag']);
      $out[] = array('id' => $res['spieltag'], 'name' => $name);
    }

    return $out;
  }
}

?>
