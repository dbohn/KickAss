<?php
/**
 * Query für Postgres Datenbanken
 *
 * @author Luca Keidel
 */
namespace API;

/**
 * Query für Postgres Datenbanken
 */
class PGQuery implements IQuery{

  /**
   * @var PDO $db Datenbank-Objekt
   */
  private $db;

  /**
   * @var PDOStatement $query PDO Statement
   */
  private $query;

  /**
   * @var Array $params Parameter
   */
  private $params = array();

  /**
   * @var Array $bind_params Parameter für das Query
   */
  private $bind_params = array();

  /**
   * Initialisiert die Datenbankverbindung
   *
   * Aufruf am Anfang jeder Funktion dieser Klasse. Es wird eine Datenbankverbindung initialisiert
   * falls noch keine besteht.
   */
  private function init(){
    if (!class_exists('PDO')){
      throw new Exception('PDO is not available. Make sure you have enabled it in your config.');
    }

    try{
      $this -> db = new \PDO(
        'pgsql:dbname='.Config::$db_name.
        ';host='.Config::$db_host.
        ';user='.Config::$db_user.
        ';password='.Config::$db_pass
      );
    }catch(\Exception $e){
      throw new \Exception('Database connection failed!');
    }
  }

  /**
   * Binden der Parameter
   *
   * @param Array $params Parameter zum Binden 
   */
  public function bind($params){
    $this -> bind_params = $params;
  }

  /**
   * Konstruktur, Erzeugen des Prepared Statements
   *
   * @param string $sql Das SQL-Query
   */
  public function __construct($sql){
    self::init();
    $this -> query = $this -> db-> prepare($sql);
  }

  /**
   * Ausführen des Querys
   *
   * @param Array $params
   *
   * @return Array Ergebnisse der Abfrage
   */
  public function exec($params = array()){
    if(count($params) == 0){
      $params = $this -> bind_params;
      $this -> params = $this-> bind_params;
    }
    $ex = $this->query->execute($params);
    $this -> params = $params;

    if(!$ex){
      $err = $this -> db -> errorInfo();
      throw new \Exception($err[2]);
    }


    return $this ->query->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Getter für das verwendete SQL Query
   */
  public function getSQL(){
    return $this -> interpolateQuery($this -> query -> queryString, $this -> params);
  }



  /**
   * Interpoliert das Query
   *
   * Adaptiert von http://stackoverflow.com/a/1376838/3289455. PDO bietet leider
   * keine Möglichkeit, das fertige Query zu extrahieren, aus diesem Grund muss
   * diese Hilfsfunktion verwendet werden.
   *
   * @param string $query Das ursprüngliche Query
   * @param Array $params Die Parameter, die im Query gebunden werden
   *
   * @return string Das interpolierte Query
   */
  private function interpolateQuery($query, $params) {
    $keys = array();

    # build a regular expression for each parameter
    foreach ($params as $key => $value) {
        if (is_string($key)) {
            $keys[] = '/:'.$key.'/';
        } else {
            $keys[] = '/[?]/';
        }
    }

    $query = preg_replace($keys, $params, $query, 1, $count);

    return $query;
  }
}

?>
