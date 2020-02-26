<?php

namespace GLEYSON\DB;

class Sql{

  /* MY SQL CONECTION
  const HOST = "127.0.0.1:3307";
  const USERNAME = 'root';
  const PASSWORD = '';
  const DB = 'db_apirest';
  */

  private $conn;

  public function __construct(){

    /*
    MYSQL CONNECTION
    $this->conn = new \PDO(
      "mysql:dbname=".Sql::DB.";host=".Sql::HOST,
      Sql::USERNAME,
      Sql::PASSWORD
    );*/

    $db = (parse_url(getenv('DATABASE_URL') ?: 'postgresql://postgres:32841516@localhost:5432/api_rest'));

    $this->conn = new \PDO("pgsql:" . "host=".$db['host'].";port=".$db['port'].";user=".$db['user'].";password=".$db['pass'].";dbname=".ltrim($db["path"], "/"));

  }

  private function setParams($statment, $parameters = array()){

    foreach($parameters as $key => $value){
      $this->bindParam($statment, $key, $value);
    }

  }

  private function bindParam($statment, $key, $value){

    $statment->bindParam($key, $value);

  }

  public function query($rawQuery, $params = array()){

    $stmt = $this->conn->prepare($rawQuery);
    $this->setParams($stmt, $params);
    $stmt->execute();
    return $stmt->rowCount();

  }

  public function select($rawQuery, $params = array()):array{

    $stmt = $this->conn->prepare($rawQuery);
    $this->setParams($stmt, $params);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);

  }

}