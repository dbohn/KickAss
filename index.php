<?php
/**
* KickAss - DBS-Projekt SoSe 2014 FU-Berlin
*
* Routing für die Weboberfläche der Anwendung
*
* @author David Bohn
* @author Luca Keidel
*
*/

/**
 * Autoloader
 */
require 'vendor/autoload.php';

/**
 * @var \Slim\Slim Die Slim Application
 */
$app = new \Slim\Slim(array(
    'view' => new \Slim\Views\Twig(),
    'templates.path' => 'views'
));

$app->setName('KickAss');

$app->view()->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

$app->get('/', function () use ($app) {
    $app->render('index.twig');
});

$app->get('/documentation(/:page)', function($page = null) use ($app) {
	$response = $app->response();
	if ($page === null) {
		$files = glob('docs/*.md');
		$files = array_map(function($el) {
			$base = basename($el);
			$ref = (substr($base, 0, strpos($base, '.md')));

			$title = titleForFile($el, $ref);
			return array('ref' => $ref, 'title' => $title);
		}, $files);
  		$resp = array('pages' => $files);
	} else {
		$file = basename($page);
		$path = 'docs/' . $file . '.md';
		if (@file_exists($path)) {
			$markdown = file_get_contents($path);
			$parsed = \Michelf\MarkdownExtra::defaultTransform($markdown);

			$title = titleForFile($path, $file);
			$resp = array('docs' => $parsed, 'title' => $title, 'ref' => $file);
		} else {
			$response->setStatus(404);
			$resp = array('error' => 'Page not found!');
		}
	}

	$response['Content-Type'] = 'application/json';

	$response->body(json_encode($resp, JSON_PRETTY_PRINT));
});

/**
 * Liest den Titel aus einer Datei im Markdown Format aus.
 *
 * @param string $file Dateipfad zur Markdown-Datei
 * @param string $default Standardwert für den Titel
 *
 * @return string Titel für die angegebene Datei
 */
function titleForFile($file, $default)
{
	$fp = fopen($file, 'r');
	$title = $default;
	if ($fp) {
		$title = ltrim(fgets($fp), '#');
		fclose($fp);
	}

	return $title;
}

$app->run();
?>
