#! /usr/bin/php

<?php

/**
 * KickAss Import
 *
 * Programmatischer Import für die CSV Daten von @link{http://dbup2date.uni-bayreuth.de/bundesliga.html}
 *
 * @author Luca Keidel
 * @author David Bohn
 */

/**
 * Autoloader
 */
require '../vendor/autoload.php';

use League\Csv\Reader;
use API\Config;

/**
 * KickAss Import
 *
 * Import Klasse für den programmatischen Import von den CSV
 * Daten
 */
class Import{

  /**
   * @var Array $add Bereitstellungen zusätzlicher Informtationen, die aus den Daten nicht hervorgehen
   */
  private $add = array(
    array(                            // Saison für die 1. Liga
      'saison_start' => '2013-08-09',
      'saison_ende' => '2014-05-10'
    ),
    array(                            // Saison für die 2. Liga
      'saison_start' => '2013-07-19',
      'saison_ende' => '2014-05-11'
    ),
    array(                            // Saison für die 3. Liga
      'saison_start' => '2013-07-19',
      'saison_ende' => '2014-05-10'
    )
  );

  /**
   * @var string $path_liga Pfad zur CSV-Datei mit den Ligen
   */
  private $path_liga;

  /**
   * @var string $path_spiel Pfad zur CSV-Datei mit den Spielen
   */
  private $path_spiel;

  /**
   * @var string $path_spieler Pfad zur CSV-Datei mit den Spielern
   */
  private $path_spieler;

  /**
   * @var string $path_verein Pfad zur CSV-Datei mit den Vereinen
   */
  private $path_verein;

  /**
   * @var \PDO $dbpath Das verwendete PDO Objekt
   */
  private $dbpath;

  /**
   * Import Konstruktor, initialisiert den Import.
   *
   * @param string $path_liga Pfad zur Liga CSV-Datei
   * @param string $path_spiel Pfad zur Spiel CSV-Datei
   * @param string $path_spieler Pfad zur Spieler CSV-Datei
   * @param string $path_verein Pfad zur Verein CSV-Datei
   */
  public function __construct($path_liga, $path_spiel, $path_spieler, $path_verein){
    $this->db_host = Config::$db_host;
    $this->db_user = Config::$db_user;
    $this->db_password = Config::$db_pass;
    $this->db_name = Config::$db_name;

  	if($this -> db_user == '' || $this -> db_password == ''){
  		$this -> genericError('Please edit this file and specify a database user and password!');
  	}

    $this -> path_liga = $path_liga;
    if(!file_exists($this -> path_liga)){
      $this -> fileError('Liga', $path_liga);
    }

    $this -> path_spiel = $path_spiel;
    if(!file_exists($this -> path_spiel)){
      $this -> fileError('Spiel', $path_spiel);
    }

    $this -> path_spieler = $path_spieler;
    if(!file_exists($this -> path_spieler)){
      $this -> fileError('Spieler', $path_spieler);
    }

    $this -> path_verein = $path_verein;
    if(!file_exists($this -> path_verein)){
      $this -> fileError('Verein', $path_verein);
    }

    if (!class_exists('PDO')){
      $this -> genericError('PDO is not available. Make sure you have enabled it in your config.');
    }
    try{
      $this -> dbpath = new PDO(
      'pgsql:dbname='.$this->db_name.
      ';host='.$this->db_host.
      ';user='.$this->db_user.
      ';password='.$this->db_password
    );
    }catch(\Exception $e){
      $this -> genericError($e -> getMessage());
    }


  }

  /**
   * Startet den Import
   *
   */
  public function run(){

    printf("Starting the import... \n");

    $db = $this -> dbpath; //for convenience

    printf("Importing into table Liga\n");

    $reader_liga = new Reader($this -> path_liga);
    $reader_liga->setDelimiter(';');
    $reader_liga->setEncoding('iso-8859-1');

    $liga_total = count($reader_liga->setOffset(1)->fetchAll());
    $i = 0;
    $liga = $reader_liga->setOffset(1);

    $sth = $db->prepare('INSERT INTO Liga(id, name) VALUES (:id, :name)');
    $liga->each(function ($row) use (&$sth, &$i, &$liga_total, &$db) {

        $liga_id = $row[0];
        if($liga_id != 3){
          $name = $liga_id.'. Bundesliga';
        }else{
          $name = $liga_id.'. Liga';
        }

        $sth->bindValue(':id', utf8_encode($liga_id), PDO::PARAM_STR);
        $sth->bindValue(':name', utf8_encode($name), PDO::PARAM_STR);

        $ret = $sth->execute();
        if(!$ret){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $this -> progress(++$i,$liga_total);
        return $ret;

    });


    // Insert Saison (additional)
    printf("\n\n");
    printf("Inserting Saison (additional information)\n");

    $saison_ids = array();
    $sth = $db->prepare('INSERT INTO Saison(start_datum, end_datum, liga) VALUES (:start, :end, :liga) RETURNING id');

    for($i = 1; $i <= 3; $i++){

      $sth->execute(array(
        'start' => $this -> add[($i-1)]['saison_start'],
        'end' => $this -> add[($i-1)]['saison_ende'],
        'liga' => $i

      ));

      $this->progress($i,3);

      $saison = $sth->fetch(\PDO::FETCH_ASSOC);
      $saison_ids[($i-1)] = $saison['id'];
    }

    printf("\n\n");
    printf("Importing into table Verein (creating Teams on the fly)\n");

    $reader_verein = new Reader($this -> path_verein);
    $reader_verein->setDelimiter(';');
    $reader_verein->setEncoding('iso-8859-1');

    $verein_total = count($reader_verein->setOffset(1)->fetchAll());
    $i = 0;
    $verein = $reader_verein->setOffset(1);

    $sth = $db->prepare('INSERT INTO Verein(id, name) VALUES (:id, :name)');
    $sth_team = $db->prepare(
    'INSERT INTO Team(id, name, verein_id, liga_id, saison_id)
     VALUES (:id, :name, :verein, :liga, :saison)');
    $verein->each(function ($row) use (&$sth, &$i, &$verein_total, &$sth_team, &$db, &$saison_ids) {

        $sth->bindValue(':id', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth->bindValue(':name', utf8_encode($row[1]), PDO::PARAM_STR);

        if(!$sth->execute()){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $sth_team->bindValue(':id', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth_team->bindValue(':name', utf8_encode($row[1]), PDO::PARAM_STR);
        $sth_team->bindValue(':verein', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth_team->bindValue(':liga', utf8_encode($row[2]), PDO::PARAM_STR);
        $sth_team->bindValue(':saison', $saison_ids[intval($row[2])-1], PDO::PARAM_STR);

        $ret = $sth_team->execute();
        if(!$ret){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $this->progress(++$i,$verein_total);
        return $ret;

    });

    printf("\n\n");
    printf("Importing into table Spiele\n");

    $reader_spiel = new Reader($this -> path_spiel);
    $reader_spiel->setDelimiter(';');
    $reader_spiel->setEncoding('iso-8859-1');

    $spiel_total = count($reader_spiel->setOffset(1)->fetchAll());
    $i = 0;
    $spiel = $reader_spiel->setOffset(1);
    $sth_liga = $db->prepare('SELECT liga_id FROM Team WHERE id = :id');
    $sth = $db->prepare('INSERT INTO Spiel(id, anpfiff_datum, ort, spieldauer, saison_id, liga_id, gast_id, gastgeber_id, spieltag, toreHeim, toreGast) VALUES (:id, :datum, :ort, :dauer, :saison, :liga, :gast, :gastgeber, :spieltag, :toreHeim, :toreGast)');
    $spiel->each(function ($row) use (&$sth, &$i, &$spiel_total, &$db, &$saison_ids, &$sth_liga) {

        $sth_liga->bindValue(':id', utf8_encode($row[4]), PDO::PARAM_STR);

        if(!$sth_liga->execute()){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $ligadata = $sth_liga -> fetch(\PDO::FETCH_ASSOC);
        $liga_id = $ligadata['liga_id'];

        $datum = $row[2].' '.$row[3];

        $sth->bindValue(':id', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth->bindValue(':spieltag', utf8_encode($row[1]), PDO::PARAM_STR);
        $sth->bindValue(':datum', utf8_encode($datum), PDO::PARAM_STR);
        $sth->bindValue(':ort', 'Unknown', PDO::PARAM_STR);
        $sth->bindValue(':dauer', -1, PDO::PARAM_STR);
        $sth->bindValue(':saison', $saison_ids[intval($liga_id)-1], PDO::PARAM_STR);
        $sth->bindValue(':liga', $liga_id, PDO::PARAM_STR);
        $sth->bindValue(':gast', utf8_encode($row[5]), PDO::PARAM_STR);
        $sth->bindValue(':gastgeber', utf8_encode($row[4]), PDO::PARAM_STR);
        $sth->bindValue(':toreHeim', utf8_encode($row[6]), PDO::PARAM_STR);
        $sth->bindValue(':toreGast', utf8_encode($row[7]), PDO::PARAM_STR);

        $ret = $sth->execute();
        if(!$ret){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $this -> progress(++$i,$spiel_total);
        return $ret;

    });

    printf("\n\n");
    printf("Importing into table Spieler and erzielt_tor and spielt_bei\n");

    $reader_spieler = new Reader($this -> path_spieler);
    $reader_spieler->setDelimiter(';');
    $reader_spieler->setEncoding('iso-8859-1');

    $spieler_total = count($reader_spieler->setOffset(1)->fetchAll());
    $i = 0;
    $spieler = $reader_spieler->setOffset(1);

    $sth = $db->prepare('INSERT INTO Spieler(id, name, heimatland) VALUES (:id, :name, :land)');
    $sth_liga = $db->prepare('SELECT liga_id FROM Team WHERE id = :id');
    $sth_tore = $db->prepare('INSERT INTO erzielt_tore(spieler_id, saison_id, anzahl) VALUES (:spielerid, :saisonid, :tore)');
    $sth_spieltbei = $db->prepare('INSERT INTO spielt_bei(team_id, spieler_id, trikotnr) VALUES (:teamid, :spielerid, :trikotnr)');
    $spieler->each(function ($row) use (&$sth, &$i, &$spieler_total, &$db, &$sth_tore, &$sth_liga, &$saison_ids, &$sth_spieltbei) {

        $sth->bindValue(':id', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth->bindValue(':name', utf8_encode($row[3]), PDO::PARAM_STR);
        $sth->bindValue(':land', utf8_encode($row[4]), PDO::PARAM_STR);

        if(!$sth->execute()){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $sth_liga->bindValue(':id', utf8_encode($row[1]), PDO::PARAM_STR);
        if(!$sth_liga->execute()){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $ligadata = $sth_liga -> fetch(\PDO::FETCH_ASSOC);
        $liga_id = $ligadata['liga_id'];

        $sth_spieltbei->bindValue(':teamid', utf8_encode($row[1]), PDO::PARAM_STR);
        $sth_spieltbei->bindValue(':spielerid', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth_spieltbei->bindValue(':trikotnr', utf8_encode($row[2]), PDO::PARAM_STR);

        if(!$sth_spieltbei->execute()){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $sth_tore->bindValue(':saisonid', $saison_ids[intval($liga_id)-1], PDO::PARAM_STR);
        $sth_tore->bindValue(':spielerid', utf8_encode($row[0]), PDO::PARAM_STR);
        $sth_tore->bindValue(':tore', utf8_encode($row[6]), PDO::PARAM_STR);

        $ret = $sth_tore->execute();
        if(!$ret){
          $err = $db -> errorInfo();
          $this -> genericError($err[2]);
        }

        $this -> progress(++$i,$spieler_total);
        return $ret;

    });

    printf("\n\n");
    printf("Import finished!\n");
  }

  /**
   * Gibt eine Fehlermeldung auf dem STDERR Kanal aus
   * und beendet die Programmausführung.
   *
   * @param string $msg Fehlermeldung
   */
  private function genericError($msg){
    fprintf(STDERR, "ERROR: %s\n", $msg);
    exit(-1);
  }

  /**
   * Anzeigen eines Fortschrittsbalkens
   *
   * @param int $w Wert
   * @param int $g Grundwert
   */
  private function progress($w, $g){
    $percent = round(($w / $g) * 100);
    $bar_segs = round($percent / 100 * 60);
    $bar = '';
    for($i = 0; $i < $bar_segs; $i++){
      $bar.='=';
    }
    printf("\r%3d%% [%-60s] %d/%d", $percent, $bar, $w, $g);
  }

  /**
   * Spezielle Fehlermeldung für den Fall, dass eine der benötigten Dateien fehlt
   *
   * @param string $descr Beschreibung der Datei die fehlt
   * @param string $file Der fehlerhafte Dateipfad 
   */
  private function fileError($descr, $file){
    fprintf(STDERR, "%s: The file %s does not exist!\n", $descr, $file);
    exit(-1);
  }
}


if(count($argv) != 5){
  fprintf(STDERR, "Usage: %s <Liga> <Spiel> <Spieler> <Verein> \n", $argv[0]);
  exit(-1);
}

$i = new \Import($argv[1], $argv[2], $argv[3], $argv[4]);
$i->run();

?>
