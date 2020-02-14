<?php
namespace GLEYSON\DB;

class Sql{
  const HOST = "127.0.0.1:3307";
  const USERNAME = 'root';
  const PASSWORD = '';
  const DB = 'db_apirest';

  private $conn;

  public function __construct(){

    $this->conn = new \PDO(
      "mysql:dbname=".Sql::DB.";host=".Sql::HOST,
      Sql::USERNAME,
      Sql::PASSWORD
    );

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