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

// begin group player

$app->group('/player',function () use ($app) {

    $app->get('/playergoals/:tore', function($tore) use ($app){
      try{
        $a = new \API\Answer\Playergoals($tore);
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/team/:teamid', function($teamid) use ($app){
      try{
        $a = new \API\Answer\Team($teamid);
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });
});

// end group player

// begin group team

$app->group('/team',function () use ($app) {

    $app->get('/won/:teamid', function($teamid) use ($app){
      try{
        $a = new \API\Answer\Won($teamid);
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });


    $app->get('/loser', function() use ($app){
      try{
        $a = new \API\Answer\Loser();
        resp($a->getSQL(),$a->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });
});

// end group team

$app->get('/tabelle(/:liga)', function ($liga = 1) use ($app){
try{
  $a = new \API\Answer\Tabelle($liga);
  resp($a->getSQL(),$a->getData());
}catch(Exception $e){
  error($e->getMessage());
}
});

$app->get('/arff(/:opt)', function ($opt = NULL) use ($app){

  $loader = new Twig_Loader_Filesystem('Arff/templates');
  $twig = new Twig_Environment($loader);
try{
  $a = new \API\Arff\Arff();
  $data = $a -> getData();
  $arff = $twig->render('bundesliga.twig', array('data' => $data['data'], 'vereine' => $data['vereine']));

  if($opt != 'download'){
    resp($a->getSQL(),$arff);
  }else{
    resp($a->getSQL(), $arff, false, false, 'KickAss_Bundesliga.arff', strlen($arff));
  }

;}catch(Exception $e){
  error($e->getMessage());
}
});


$app->group('/list',function () use ($app) {

    $app->get('/saison', function() use ($app){
      try{
        $l = new \API\Lists\Saison();
        resp($l->getSQL(),$l->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    // List for Verein
    $app->get('/verein(/:liga)', function($liga = NULL) use ($app){
      try{
        $l = new \API\Lists\Verein($liga);
        resp($l->getSQL(),$l->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/liga', function() use ($app){
      try{
        $l = new \API\Lists\Liga();
        resp($l->getSQL(),$l->getData());
      }catch(Exception $e){
        error($e->getMessage());
      }
    });

    $app->get('/spieltag(/:liga)', function($liga = NULL) use ($app){
      try{
        $l = new \API\Lists\Spieltag($liga);
        resp($l->getSQL(),$l->getData());
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

function resp($sql, $payload, $error = false, $json = true, $filename = NULL, $contentlength = 0){
  global $app, $resp;

  $response = $app->response();

  if($json){
    $response['Content-Type'] = 'application/json';

    $resp['status'] = $error? 'error' : 'ok';
    $resp['sql'] = $sql;
    $resp['payload'] = $payload;

    $response->body(json_encode($resp, JSON_PRETTY_PRINT));

  }else{

    $response['Content-Type'] = 'application/octet-stream';
    $response['Content-Disposition'] = 'attachment; filename="'.$filename.'"';
    $response['Content-Length'] = $contentlength;

    $response->body($payload);
  }


}


?>
