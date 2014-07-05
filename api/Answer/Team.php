<?php

/**
 * Antwort für Frage #4
 *
 * @author Luca Keidel
 */

namespace API\Answer;

/**
 * Antwort für Frage #4
 */
class Team extends \API\Answer\Answer{

  /**
   * Konstruktor: führt die Abfrage durch
   *
   * @param int $team ID des Teams
   */
  public function __construct($team){
    parent::__construct('SELECT name, heimatland, anzahl AS tore, trikotnr FROM Spieler, erzielt_tore, spielt_bei WHERE Spieler.id = erzielt_tore.spieler_id AND spielt_bei.team_id = :team AND spielt_bei.spieler_id = Spieler.id ORDER BY trikotnr');

    $this -> execute(array('team' => $team));
  }
}


?>
