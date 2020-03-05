<?php
namespace GLEYSON\Model;

use \GLEYSON\Model;
use \GLEYSON\DB\Sql as SQL;
use \Firebase\JWT\JWT;

class User extends Model{

  protected $fields = [
    "id", "name", "lastname", "company", "email", "password"
  ];

  // Permissions: 1 - ADD, 2 - DELETE, 3 - UPDATE, 4 - READ
  protected $permissions = [
    1, 2, 3, 4
  ];

  private $date_today;
  private $conn;
  private $token;

  function __construct(){
    $this->date_today = date("d-m-Y");
    $this->conn = new SQL();
  }

  private function verifyToken($token, $permission = 0):bool{

    $token_result = $this->conn->select('SELECT * FROM token AS T
    INNER JOIN permissions AS P
    ON P.id_token = T.id
    WHERE T.token = :token AND T.valid >= :date_today
    AND P.permission = :permission', array(
      ":token" => $token,
      ":date_today" => strtotime($this->date_today),
      ":permission" => $permission
    ));


    if(count($token_result) > 0){

      return true;

    }else{

      return false;

    }

  }

  public function getAllUsers($token){

    $result_token = $this->verifyToken($token, $this->permissions[3]);

    if($result_token === true){

      $result = $this->conn->select("SELECT * FROM users");

      if($result == null || count($result) == 0){
        return json_encode(
          array(
            "message"=>"No data found"
          )
          );
      }else{
        return json_encode($result);
      }

    }else{

      return json_encode(
        array(
          "message"=>"Token invalid or expired or permission deneid"
        )
        );

    }

  }

  public function getParticularUser($id, $token){

    $result_token = $this->verifyToken($token, $this->permissions[3]);

    if($result_token === true){

      $result = $this->conn->select("SELECT * FROM users WHERE id = :id", array(
        ":id" => $id
      ));

      if($result != null || count($result) > 0){

        $this->setData($result[0]);
        return json_encode($result[0]);

      }else{
        return json_encode(
          array(
            "message"=>"No data found"
          )
          );
      }

    }else{
      return json_encode(
        array(
          "message"=>"Token invalid or expired or permission deneid"
        )
        );
    }

  }

  public function addUser($token){

    $result_token = $this->verifyToken($token, $this->permissions[0]);

    if($result_token === true){

      $result = $this->conn->query("INSERT INTO users (name, lastname, company, email, password)
      VALUES(:name, :lastname, :company, :email, :password)", array(
        ":name" => $this->getname(),
        ":lastname" => $this->getlastname(),
        ":company" => $this->getcompany(),
        ":email" => $this->getemail(),
        ":password" => $this->getpassword()
      ));

      if($result > 0){
        return json_encode(
          array(
            "message"=>"User added with success!"
          )
          );
      }else{
        return json_encode(
          array(
            "message"=>"Error registering user"
          )
          );
      }

    }else{

      return json_encode(
        array(
          "message"=>"Token invalid or expired or permission deneid"
        )
        );

    }

  }

  public function deleteUser($token){

    $result_token = $this->verifyToken($token, $this->permissions[1]);
    if($result_token === true){

      $result = $this->conn->query("DELETE FROM users WHERE
      id in (SELECT id FROM users WHERE id = :id)", array(
        ":id" => $this->getid()
      ));

      if($result > 0){

        return json_encode(
          array(
            "message"=>"User deleted"
          )
          );

      }else{
        return json_encode(
          array(
            "message"=>"User doesn't exists"
          )
          );
      }

    }else{
      return json_encode(
        array(
          "message"=>"Token invalid or expired or permission deneid"
        )
        );
    }

  }

  public function updateUser($id, $token){

    $result_token = $this->verifyToken($token, $this->permissions[2]);

    if($result_token){

      $result = $this->conn->query("UPDATE users SET name = :name, lastname = :lname, company = :cpn WHERE id = :id", array(
        ":name" => $this->getname(),
        ":lname" => $this->getlastname(),
        ":cpn" => $this->getcompany(),
        ":id" => $id
      ));

      if($result > 0){
        return json_encode(
          array(
            "message"=>"User updated"
          )
          );
      }else{
        return json_encode(
          array(
            "message"=>"User doesn't exists or Error"
          )
          );
      }

    }else{
      return json_encode(
        array(
          "message"=>"Token invalid or expired or permission deneid"
        )
        );
    }
  }

  public static function login($email, $password){
    
    $sql = new SQL();
    $result = $sql->select("SELECT id, name, lastname, company, email FROM users WHERE email = :e AND password = :pass",
    array(
      ":e" => $email,
      ":pass" => $password
    ));

    if(count($result) === 0){
      return json_encode(array(
        "message" => "User inválid!"
      ));
      exit;
    }

      $secret_key = "YOUR_SECRET_KEY";
      $issuer_claim = "THE_ISSUER";
      $audience_claim = "THE_AUDIENCE";
      $issuedat_claim = 1356999524; // issued at
      $notbefore_claim = 1357000000; //not before
      $token = array(
        "iss"=> $issuer_claim,
        "aud"=> $audience_claim,
        "iat"=> $issuedat_claim,
        "nbf"=> $notbefore_claim,
        "data"=> array(
          "id"=> $result[0]["id"],
          "lastname"=> $result[0]["lastname"],
          "firstname"=> $result[0]["name"],
          "email"=> $result[0]["email"]
        )
      );
      $jwt = JWT::encode($token, $secret_key);
      

    return json_encode(
      array(
          "message"=> "Login success!",
          "jwt"=> $jwt
        )
    );



  }

}
