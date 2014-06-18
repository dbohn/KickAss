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

    $app->get('/spieltag/:spieltag/:hour/:minute', function($spieltag, $hour, $minute) use ($app){

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
        $name = 'Saison '.$start->format('y').'/'.$end->format('y').' '.$res['name'];
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
