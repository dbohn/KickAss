<?php
/**
 * Antwort für Frage #6
 *
 * @author Luca Keidel
 */

namespace API\Answer;

/**
 * Antwort für Frage #6
 */
class Loser extends \API\Answer\Answer{

  /**
   * Konstruktor: führt die Abfrage durch
   */
  public function __construct(){

$query=<<<QUERY
SELECT q3.id, q3.name, q3.verloren, sb.trikotnr, sb.spieler_id, Spieler.name as Spielername FROM Team, spielt_bei as sb, Spieler, (SELECT * FROM
(SELECT Verein.id, Verein.name, COUNT(Spiel.id) AS verloren FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = Spiel.gastgeber_id AND Spiel.toreHeim < Spiel.toreGast) OR (Verein.id = Spiel.gast_id AND Spiel.toreGast < Spiel.toreHeim)
    GROUP BY Verein.id

    ORDER BY verloren DESC) as q1
LEFT JOIN
(SELECT MAX(verloren) as mx FROM (SELECT Verein.id, Verein.name, COUNT(Spiel.id) AS verloren FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = Spiel.gastgeber_id AND Spiel.toreHeim < Spiel.toreGast) OR (Verein.id = Spiel.gast_id AND Spiel.toreGast < Spiel.toreHeim)
    GROUP BY Verein.id

    ORDER BY verloren DESC) AS p) as q2

on q1.verloren = q2.mx) as q3 WHERE q3.verloren = q3.mx and Team.verein_id = q3.id and sb.team_id = Team.id and sb.spieler_id = Spieler.id
QUERY;
    parent::__construct($query);
    $this -> execute();
  }
}


?>
