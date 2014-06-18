<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => 'views'
));

$app->setName('KickAss');

$app->view()->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$app->get('/', function () use ($app) {
    //echo "Hello, $name";
    $app->render('index.twig');
});

$app->get('/lars', function () use ($app) {
    echo "Hello, Lars";
});

$app->run();
?>
