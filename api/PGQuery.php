<?php

namespace API;

class PGQuery implements IQuery{

  private $db;
  private $query;
  private $params = array();
  private $bind_params = array();

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

  public function bind($params){
    $this -> bind_params = $params;
  }

  public function __construct($sql){
    self::init();
    $this -> query = $this -> db-> prepare($sql);
  }

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


  public function getSQL(){
    return $this -> interpolateQuery($this -> query -> queryString, $this -> params);
  }


  // Source: http://stackoverflow.com/a/1376838/3289455
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

    #trigger_error('replaced '.$count.' keys');

    return $query;
  }
}

?>
