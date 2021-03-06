<?php
/**
 * Abstrakte Klasse für Antworten
 *
 * @author Luca Keidel
 */

namespace API\Answer;

/**
 * Abstrakte Klasse für Antworten.
 *
 * Jede Antwort stellt auch Daten bereit, daher handelt es sich
 * bei jeder Antwort um einen DataProvider (@see \API\IDataProvider).
 */
abstract class Answer implements \API\IDataProvider{

  /**
   * @var PGQuery $query Das Query-Objekt
   */
  private $query;

  /**
   * @var Array $data Speichert das Ergebnis der Abfrage
   */
  private $data;

  /**
   * Konstruktor, initialisiert das Query.
   *
   * @param string $sqlstring SQL-Befehl
   */
  public function __construct($sqlstring){
    $this->query = new \API\PGQuery($sqlstring);
  }

  /**
   * Führt das Query aus und speichert das Ergebnis in $data.
   *
   * @param Array $params Parameter, die dann im Query gebunden werden.
   */
  protected function execute($params = array()){
    $this->data = $this->query->exec($params);
  }

  /**
   * Getter für die Daten
   */
  public function getData(){
    return $this->data;
  }

  /**
   * Getter für das verwendete SQL Query
   */
  public function getSQL(){
    return $this->query->getSQL();
  }

}

?>
