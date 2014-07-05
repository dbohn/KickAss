<?php
/**
 * Liste mit Vereinen
 *
 * Es werden entweder alle Vereine aufgelistet oder nur die Vereine, die
 * in einer bestimmten Liga spielen.
 *
 * @author Luca Keidel
 */
namespace API\Lists;

/**
 * Liste mit Vereinen
 */
class Verein extends \API\Lists\Lists{

  /**
   * Konstruktor, führt die Abfrage aus
   *
   * @param int $liga ID der Liga
   */
  public function __construct($liga){
    if($liga){
      parent::__construct('SELECT Verein.id, Verein.name FROM Verein, Team WHERE Team.liga_id=:liga AND Team.verein_id = Verein.id');
      $this->execute(array('liga' => $liga));
    }else{
      parent::__construct('SELECT id, name FROM Verein');
      $this->execute();
    }
  }

  /**
   * Getter für die Liste
   */
  public function getList(){
    return $this->list;
  }
}

?>
