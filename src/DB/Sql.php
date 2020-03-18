<?php

namespace GLEYSON\DB;

class Sql
{

  const HOST = "localhost";
  const USERNAME = 'postgres';
  const PASSWORD = '32841516';
  const DB = 'api_rest';
  const PORT = '5432';
  const CONNECTION = 0; // 0 = pgsql, 1 = mysql

  private $conn;

  public function __construct()
  {

    try{

      switch(Sql::CONNECTION){
        case 0:
          $db = (parse_url(getenv('DATABASE_URL') ?: 'mysql://'.Sql::USERNAME.':'.Sql::PASSWORD.'@'.Sql::HOST.':'.Sql::PORT.'/'.Sql::DB));
          $this->conn = new \PDO("pgsql:host=".$db['host'].";port=".$db['port'].";user=".$db['user'].";password=".$db['pass'].";dbname=".ltrim($db["path"], "/"));
          break;
        case 1:
          $this->conn = new \PDO("mysql:dbname=".Sql::DB.";host=".Sql::HOST, Sql::USERNAME, Sql::PASSWORD);
          break;
        default:
          break;
      }

    }catch (PDOException $e){
      return json_encode(array(
        "message" => $e->getMessage()
      ));
    }

  }

  private function setParams($statment, $parameters = array())
  {

    foreach($parameters as $key => $value){
      $this->bindParam($statment, $key, $value);
    }

  }

  private function bindParam($statment, $key, $value)
  {

    $statment->bindParam($key, $value);

  }

  public function query($rawQuery, $params = array())
  {

    $stmt = $this->conn->prepare($rawQuery);
    $this->setParams($stmt, $params);
    $stmt->execute();
    return $stmt->rowCount();

  }

  public function select($rawQuery, $params = array()):array
  {

    $stmt = $this->conn->prepare($rawQuery);
    $this->setParams($stmt, $params);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);

  }

}