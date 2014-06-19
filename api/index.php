<?php
require '../vendor/autoload.php';

$resp = array(
  'status' => NULL, // (ok|error)
  'sql' => NULL,  // SQL Query
  'payload' => NULL, // Data
);

$app = new \Slim\Slim();

$app->setName('KickAss API');

$app->get('/', function() use ($app){
  error('This is the KickAss API. Please refer to the documentation.');
});

// begin group games

$app->group('/games',function () use ($app) {

    $app->get('/firstgame/:identifier', function($identifier) use ($app){
      try{
        $a = new \API\Answer\Firstgame($identifier);
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/spieltag/:spieltag(/:hour/:minute)', function($spieltag, $hour = 0, $minute = 0) use ($app){
      try{
        $a = new \API\Answer\Spieltag($spieltag, $hour, $minute);
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

});

// end group games

$app->get('/tabelle(/:liga)', function ($liga = 1) use ($app){
try{
  $a = new \API\Answer\Tabelle($liga);
  resp($a->getSQL(),$a->getData());
}catch(Exception $e){
  error($e->getMessage());
}
});


$app->group('/list',function () use ($app) {

    $app->get('/saison', function() use ($app){
      try{
        $l = new \API\Lists\Saison();
        resp(NULL,$l->getList());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    // List for Verein
    $app->get('/verein(/:liga)', function($liga = NULL) use ($app){
      try{
        $l = new \API\Lists\Verein($liga);
        resp(NULL,$l->getList());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/liga', function() use ($app){
      try{
        $l = new \API\Lists\Liga();
        resp(NULL,$l->getList());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/spieltag(/:liga)', function($liga = NULL) use ($app){
      try{
        $l = new \API\Lists\Spieltag($liga);
        resp(NULL,$l->getList());
      }catch(Exception $e){
        error($e->getMessage());
      }
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
