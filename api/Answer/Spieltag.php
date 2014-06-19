<?php

namespace API\Answer;

class Spieltag extends \API\Answer\Answer{

  public function __construct($spieltag, $hour, $minute){

    /* formatted query:
      SELECT anpfiff_datum, liga, gg.name AS heim, Verein.name AS gast, toreheim, toregast FROM (SELECT anpfiff_datum, Liga.name AS liga, toreheim, toregast, Verein.name as name, gast_id
        FROM Spiel, Verein, Liga
        WHERE spieltag=:spieltag
          AND DATE_PART('hour', anpfiff_datum) >= :hour
          AND DATE_PART('minute', anpfiff_datum) >= :minute
          AND Liga.id = liga_id
          AND Verein.id = gastgeber_id) AS gg , Verein WHERE gg.gast_id = Verein.id;
    */

    parent::__construct('SELECT anpfiff_datum, liga, gg.name AS heim, Verein.name AS gast, toreheim, toregast FROM (SELECT anpfiff_datum, Liga.name AS liga, toreheim, toregast, Verein.name as name, gast_id FROM Spiel, Verein, Liga WHERE spieltag=:spieltag AND DATE_PART(\'hour\', anpfiff_datum) >= :hour AND DATE_PART(\'minute\', anpfiff_datum) >= :minute AND Liga.id = liga_id AND Verein.id = gastgeber_id) AS gg , Verein WHERE gg.gast_id = Verein.id;');

    // catch trivial errors
    if($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59){
      throw new \Exception('Wrong time');
    }

    $this -> execute(array(
      'spieltag' => $spieltag,
      'hour' => $hour,
      'minute' => $minute
     ));
  }
}


?>
