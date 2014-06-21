<?php

namespace API\Arff;

class Arff implements \API\IDataProvider{

  private $vereine;

  public function __construct(){
    $q = new \API\PGQuery('SELECT id,name FROM Verein');
    $this->vereine = $q->exec();
  }

  public function getSQL(){
    return NULL;
  }

  public function getData(){

$query =<<<QUERY
SELECT tore.tore AS tore, gegen.tore AS gegentore, (VerGast.ver + VerHeim.ver) AS verloren, g5.tore AS g5, g4.tore AS g4, g3.tore AS g3, g2.tore AS g2, g1.tore AS g1
FROM

	(SELECT SUM(oq.tore) AS tore FROM
	(SELECT tore FROM
	(SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	 SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 3) AS oq) AS tore,

	(SELECT SUM(oq.tore) AS tore FROM
	(SELECT tore FROM
	(SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 3) AS oq) AS gegen,


	(SELECT COUNT(*) AS ver FROM (SELECT * FROM Spiel WHERE gast_id = :verein OR gastgeber_id = :verein ORDER BY anpfiff_datum DESC LIMIT 5) AS q
	 WHERE q.gastgeber_id = :verein AND toreHeim < toreGast) AS VerHeim,

	(SELECT COUNT(*) AS ver FROM (SELECT * FROM Spiel WHERE gast_id = :verein OR gastgeber_id = :verein ORDER BY anpfiff_datum DESC LIMIT 5) AS q
	 WHERE q.gast_id = :verein AND toreHeim > toreGast) AS VerGast,

	 (SELECT tore FROM
	 (SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	 ORDER BY q.anpfiff_datum DESC LIMIT 1) AS g1,


	(SELECT tore FROM
	 (SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 1 OFFSET 1) AS g2,


	(SELECT tore FROM
	 (SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 1 OFFSET 2) AS g3,

	(SELECT tore FROM
	 (SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 1 OFFSET 3) AS g4,

	(SELECT tore FROM
	 (SELECT anpfiff_datum, toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT anpfiff_datum, toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS q
	ORDER BY q.anpfiff_datum DESC LIMIT 1 OFFSET 4) AS g5

QUERY;

    foreach($this->vereine as $verein){

      $q = new \API\PGQuery($query);
      $data = $q->exec(array('verein' => $verein['id']));
      $data = $data[0];
      
      $out[] = array(
        'name' => $verein['name'],
        'tore' => $data['tore'],
        'gegentore' => $data['gegentore'],
        'verloren' => $data['verloren'],
        'steigung' => 0 // to be implemented
      );
    }

    return $out;
  }
}

?>
