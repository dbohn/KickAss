<?php
require '../vendor/autoload.php';

$resp = array(
  'status' => NULL, // ok or error
  'sql' => NULL,  // SQL Query
  'payload' => NULL, // Daten
);

$app = new \Slim\Slim();

$app->setName('KickAss API');

$app->get('/', function() use ($app){
  error('This is the KickAss API. Please refer to the documentation.');
});

// begin group games

$app->group('/games',function () use ($app) {

    $app->get('/firstgame/:identifier', function($identifier) use ($app){

      $match = preg_match_all('/(\d)\.(\d{4}\-\d{1,2}\-\d{1,2})/', $identifier, $matches);

      if(count($matches) != 3 || count($matches[1]) != 1 || count($matches[2]) != 1 || !is_numeric($matches[1][0])){
        error('Invalid identifier format.');
      }

      $datum = $matches[2][0];
      $liga = $matches[1][0];

      // check if date is valid
      $e = explode('-', $datum);
      if(!checkdate($e[1],$e[2], $e[0])){
        error('Invalid date.');
        exit(-1);
      }

      $q = new \API\PGQuery('SELECT anpfiff_datum FROM Spiel, Saison, Liga WHERE Saison.liga = :liga AND Spiel.saison_id = Saison.id AND Saison.start_datum = :datum ORDER BY Spieltag, anpfiff_datum LIMIT 1 ');

      try{
        $res = $q -> exec(array('datum' => $datum, 'liga' => $liga));
        $sql = $q -> getSQL();
        resp($sql,$res);
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/spieltag/:spieltag(/:hour/:minute)', function($spieltag, $hour = 0, $minute = 0) use ($app){

      // catch trivial errors
      if($hour < 0 || $hour > 23 || $minute < 0 || $minute > 59){
        error('Wrong time');
      }

      /* formatted query:
        SELECT anpfiff_datum, liga_id AS liga, gg.name AS heim, Verein.name AS gast, toreheim, toregast FROM (SELECT *
          FROM Spiel, Verein
          WHERE spieltag=:spieltag
            AND DATE_PART('hour', anpfiff_datum) >= :hour
            AND DATE_PART('minute', anpfiff_datum) >= :minute
            AND Verein.id = gastgeber_id) AS gg , Verein WHERE gg.gast_id = Verein.id;
      */

      $q = new \API\PGQuery('SELECT anpfiff_datum, liga_id AS liga, gg.name AS heim, Verein.name AS gast, toreheim, toregast FROM (SELECT * FROM Spiel, Verein WHERE spieltag=:spieltag AND DATE_PART(\'hour\', anpfiff_datum) >= :hour AND DATE_PART(\'minute\', anpfiff_datum) >= :minute AND Verein.id = gastgeber_id) AS gg , Verein WHERE gg.gast_id = Verein.id;');

      try{
        $resSet = $q -> exec(array(
          'spieltag' => $spieltag,
          'hour' => $hour,
          'minute' => $minute
         ));
        $sql = $q -> getSQL();
        resp($sql,$resSet);
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

});

// end group games

$app->get('/tabelle(/:liga)', function ($liga = 1) use ($app){

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

$q = new \API\PGQuery($query);
try{
  $resSet = $q -> exec(array('liga' => $liga));
  $sql = $q -> getSQL();
}catch(Exception $e){
  error($e->getMessage());
}

resp($sql, $resSet);

});



$app->group('/list',function () use ($app) {

    $app->get('/saison', function() use ($app){
      $q = new \API\PGQuery('SELECT start_datum, end_datum, liga, name FROM Saison, Liga WHERE Liga.id = Saison.liga');
      try{
        $resSet = $q -> exec();
        $sql = $q -> getSQL();
      }catch(Exception $e){
        error($e->getMessage());
      }

      foreach($resSet as $res){

        $start = new \DateTime($res['start_datum']);
        $end = new \DateTime($res['end_datum']);

        $id = $res['liga'].'.'.$res['start_datum'];
        $name = $res['name'].' Saison '.$start->format('y').'/'.$end->format('y');
        $out[] = array('id' => $id, 'name' => $name);

      }
      resp($sql, $out);
    });

    // List for Verein
    $app->get('/verein(/:liga)', function($liga = NULL) use ($app){
      if($liga){
        $q = new \API\PGQuery('SELECT Verein.id, Verein.name FROM Verein, Team WHERE Team.liga_id=:liga AND Team.verein_id = Verein.id');
        $q->bind(array('liga' => $liga));
      }else{
        $q = new \API\PGQuery('SELECT id, name FROM Verein');
      }

      try{
        $resSet = $q -> exec();
        $sql = $q -> getSQL();
      }catch(Exception $e){
        error($e->getMessage());
      }

      resp($sql, $resSet);
    });

    $app->get('/liga', function() use ($app){

      $q = new \API\PGQuery('SELECT id,name FROM Liga');

      try{
        $resSet = $q -> exec();
        $sql = $q -> getSQL();
      }catch(Exception $e){
        error($e->getMessage());
      }

      resp($sql, $resSet);
    });

    $app->get('/spieltag(/:liga)', function($liga = NULL) use ($app){

      if($liga){
        $q = new \API\PGQuery('SELECT DISTINCT spieltag FROM Spiel WHERE liga_id = :liga ORDER BY spieltag');
        $q->bind(array('liga' => $liga));
      }else{
        $q = new \API\PGQuery('SELECT DISTINCT spieltag FROM Spiel ORDER BY spieltag');
      }

      try{
        $resSet = $q -> exec();
        $sql = $q -> getSQL();
      }catch(Exception $e){
        error($e->getMessage());
      }

      foreach($resSet as $res){

        $name = sprintf('%2d. Spieltag', $res['spieltag']);
        $out[] = array('id' => $res['spieltag'], 'name' => $name);
      }

      resp($sql, $out);
    });
});

$app->notFound(function (){
  error('Resource not found.');
});

$app->run();

function error($msg){
  global $app;
  $err = array('status' => 'error', 'msg' => $msg);
  resp(NULL, $msg, true);
  $app->stop();
}

function resp($sql, $payload, $error = false){
  global $app, $resp;

  $response = $app->response();
  $response['Content-Type'] = 'application/json';

  $resp['status'] = $error? 'error' : 'ok';
  $resp['sql'] = $sql;
  $resp['payload'] = $payload;

  $response->body(json_encode($resp, JSON_PRETTY_PRINT));
}


?>
