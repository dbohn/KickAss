<?php
/**
 * Antwort für Frage #2
 *
 * @author Luca Keidel
 */

namespace API\Answer;

/**
 * Antwort für Frage #2
 */
class Playergoals extends \API\Answer\Answer{

  /**
   * Konstruktor: führt die Abfrage durch
   *
   * @param int $goals Minimale Anzahl von Toren
   */
  public function __construct($goals){
    parent::__construct('SELECT name, anzahl FROM Spieler, erzielt_tore WHERE anzahl > :goals AND Spieler.id = erzielt_tore.spieler_id ORDER BY anzahl DESC');

    $this -> execute(array('goals' => $goals));
  }
}


?>
