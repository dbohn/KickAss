<?php
/**
 * Abstrakte Liste
 *
 * Alle Listen liefern Informationen für die API, sind also DataProvider.
 * Außerdem spielt das verwendete SQL bei den Listen keine Rolle, daher
 * wurde die Implementierung von getSQL direkt in die abstrakte Klasse gezogen.
 *
 * @author Luca Keidel
 */

namespace API\Lists;

/**
 * Abstrakte Liste
 */
abstract class Lists implements \API\IDataProvider{

  /**
   * @var PGQuery $query Query-Objekt
   */
  private $query;
  /**
   * @var Array $list Ergebnisliste
   */
  protected $list;

  /**
   * Konstruktor, initialisiert das Query.
   *
   * @param string $sqlstring Das SQL Query
   */
  public function __construct($sqlstring){
    $this->query = new \API\PGQuery($sqlstring);
  }

  /**
   * Führt das Query aus
   *
   * @param Array $params Parameter, die dann im Query gebunden werden sollen
   */
  protected function execute($params = array()){
    $this->list = $this->query->exec($params);
  }

  /**
   * Gibt die fertig erstellte Liste zurück.
   *
   * Die Implementierung wird der jeweiligen Kindklasse überlassen,
   * da in einigen Fällen die Daten in der Liste noch interpoliert werden.
   */
  abstract public function getList();


  /**
   * Getter für die Daten
   */
  public function getData(){
    return $this -> getList();
  }

  /**
   * Getter für SQL
   */
  public function getSQL(){
    return NULL;
  }

}

?>
