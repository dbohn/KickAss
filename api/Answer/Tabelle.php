<?php

/**
 * Ergebnistabelle
 *
 * Gibt eine komplette Spieltabelle mit den Ergebnissen zu einer Liga zurück. Berücksichtigt werden dabei die insgesamt gespielten Spieltage, gewonnene, verlorene und unentschiede Spiele, die Tore und Gegentore in allen Spielen, die Differenz zwischen Toren und Gegentoren und die Punkte.
 *
 * @author Luca Keidel
 * @author David Bohn
 */

namespace API\Answer;

/**
 * Ergebnistabelle
 */
class Tabelle extends \API\Answer\Answer{

  /**
   * Konstruktur: führt die Abfrage aus
   *
   * @param int $liga ID der Liga (1|2|3)
   */
  public function __construct($liga){

$query = <<<QUERY
SELECT Verein.id, Verein.name, (gewonnen + unentschieden + verloren) as spieltage, q1.gewonnen, q2.unentschieden, q3.verloren, (q4.heimtore+q5.gasttore) AS tore, (gegenGast+gegenHeim) as gegentore, ((heimtore+gasttore) - (gegenGast+gegenHeim)) AS diff, (q1.gewonnen*3+q2.unentschieden) AS punkte
 FROM verein, team, (SELECT Verein.id, Verein.name, COUNT(Spiel.id) AS gewonnen FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = Spiel.gastgeber_id AND Spiel.toreHeim > Spiel.toreGast) OR (Verein.id = Spiel.gast_id AND Spiel.toreGast > Spiel.toreHeim)
    GROUP BY Verein.id

    ORDER BY Verein.id) as q1, (SELECT Verein.id, Verein.name, COUNT(Spiel.id) AS unentschieden FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = Spiel.gastgeber_id AND Spiel.toreHeim = Spiel.toreGast) OR (Verein.id = Spiel.gast_id AND Spiel.toreGast = Spiel.toreHeim)
    GROUP BY Verein.id

    ORDER BY Verein.id) as q2, (SELECT Verein.id, Verein.name, COUNT(Spiel.id) AS verloren FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = Spiel.gastgeber_id AND Spiel.toreHeim < Spiel.toreGast) OR (Verein.id = Spiel.gast_id AND Spiel.toreGast < Spiel.toreHeim)
    GROUP BY Verein.id

    ORDER BY Verein.id) as q3, (SELECT Verein.id, sum(toreHeim) AS heimtore FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = gastgeber_id) GROUP BY Verein.id) as q4, (SELECT Verein.id, sum(toreGast) AS gasttore FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = gast_id) GROUP BY Verein.id) as q5, (SELECT Verein.id, sum(toreHeim) AS gegenGast FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = gast_id) GROUP BY Verein.id) as q6, (SELECT Verein.id, sum(toreGast) AS gegenHeim FROM
    Verein
    LEFT JOIN Spiel on (Verein.id = gastgeber_id) GROUP BY Verein.id) as q7 WHERE verein.id = q1.id and verein.id = q2.id and verein.id = q3.id and verein.id=q4.id and verein.id=q5.id and verein.id=q6.id and verein.id=q7.id and team.verein_id = verein.id and team.liga_id=:liga
    ORDER BY punkte DESC
QUERY;

    parent::__construct($query);
    $this -> execute(array('liga' => $liga));
  }
}


?>
