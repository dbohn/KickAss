<?php
/**
 * Liste mit allen Ligen
 *
 * @author Luca Keidel
 */
namespace API\Lists;

/**
 * Liste mit allen Ligen
 */
class Liga extends \API\Lists\Lists{

  /**
   * Konstruktor, führt die Abfrage aus
   */
  public function __construct(){
    parent::__construct('SELECT id,name FROM Liga');
    $this->execute();
  }

  /**
   * Getter für die Liste
   */
  public function getList(){
    return $this->list;
  }
}

?>
