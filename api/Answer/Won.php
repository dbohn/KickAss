<?php
/**
 * Antwort für Frage #5
 *
 * @author Luca Keidel
 */

namespace API\Answer;

/**
 * Antwort für Frage #5
 */
class Won extends \API\Answer\Answer{

  /**
   * Konstruktor: führt die Abfrage durch
   *
   * @param int $teamid ID des Teams
   */
  public function __construct($teamid){
    parent::__construct('SELECT count(*) AS gewonnen  FROM Spiel WHERE (gastgeber_id = :teamid AND toreHeim > toreGast) OR (gast_id = :teamid AND toreGast > toreHeim)');

    $this -> execute(array('teamid' => $teamid));
  }
}


?>
