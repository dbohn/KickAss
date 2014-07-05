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
SELECT tore.tore AS tore, gegen.tore AS gegentore, (VerGast.ver + VerHeim.ver) AS verloren, g5.tore AS g5, g1.tore AS g1, Punkte.punkte AS punkte, ROUND(avgTore.avgTore, 2) AS avgTore, ROUND(verh.verhaeltnis::numeric, 2) AS verhaeltnis
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
	ORDER BY q.anpfiff_datum DESC LIMIT 1 OFFSET 4) AS g5,

	(SELECT (GewHeim.anz + GewAus.anz)*3 +(UnAus.anz + UnHeim.anz) AS punkte FROM
	 (SELECT COUNT(*) AS anz FROM Spiel WHERE gast_id = :verein AND toreGast > toreHeim) AS GewHeim,
	 (SELECT COUNT(*) AS anz FROM Spiel WHERE gastgeber_id = :verein AND toreHeim > toreGast) AS GewAus,
	 (SELECT COUNT(*) AS anz FROM Spiel WHERE gastgeber_id = :verein AND toreHeim = toreGast) AS UnHeim,
	 (SELECT COUNT(*) AS anz FROM Spiel WHERE gast_id = :verein AND toreHeim = toreGast) AS UnAus) AS Punkte,

	(SELECT AVG(tore) as avgTore FROM
	 (SELECT toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	  SELECT toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS Spiele) AS avgTore,

	(SELECT (CAST(gegen.tore AS float) / CAST(eigene.tore AS float)) AS verhaeltnis FROM
	 (SELECT SUM(sp.tore) AS tore  FROM
	  (SELECT toreGast AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	   SELECT toreHeim AS tore FROM Spiel WHERE gastgeber_id = :verein) AS sp) AS eigene,

	 (SELECT SUM(sp.tore) AS tore FROM
	  (SELECT toreHeim AS tore FROM Spiel WHERE gast_id = :verein UNION ALL
	   SELECT toreGast AS tore FROM Spiel WHERE gastgeber_id = :verein) AS sp) AS gegen) AS verh
QUERY;

  $list_vereine = '';

    foreach($this->vereine as $verein){

      $q = new \API\PGQuery($query);
      $data = $q->exec(array('verein' => $verein['id']));
      $data = $data[0];

      // Mittelwertsatz
      $steigung = ($data['g5'] - $data['g1']) / (5-1);

      $name = preg_replace('/\s/', '-', $verein['name']);

      $out[] = array(
        'name' => $name,
        'tore' => $data['tore'],
        'gegentore' => $data['gegentore'],
        'verloren' => $data['verloren'],
        'steigung' => $steigung,
        'punkte' => $data['punkte'],
        'avgtore' => $data['avgtore'],
        'verhaeltnis' => $data['verhaeltnis']
      );

      $list_vereine.=$name.',';
    }

    $data['data'] = $out;
    $data['vereine'] = rtrim($list_vereine, ',');

    return $data;
  }
}

?>
