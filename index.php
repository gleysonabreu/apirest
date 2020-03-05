<?php
header("Access-Control-Allow-Origin: *");
require "vendor/autoload.php";

use \GLEYSON\Model\User as USER;

$route = new \Slim\App();

// Route Auth
$route->post("/api/auth/login", function($request, $response, $args){
  
  if(isset($_POST['email']) && isset($_POST['password'])){
    $auth = USER::login($_POST['email'], $_POST['password']);
    echo $auth;
  }else{
    echo json_encode(array("message" => "Auth deneid."));
  }

  
});

// Route add particular user
$route->post("/api/add/user/{token}",
function($request, $response, $args){

  $user = new USER();
  $user->setData($_POST);
  $result = $user->addUser($args['token']);
  
  echo $result;

});

// Route update user;
$route->post("/api/update/{id}/{token}", function($request, $response, $args){
  
  $user = new USER();
  $user->setData($_POST);
  $result = $user->updateUser((int)$args['id'], $args['token']);
  
  echo $result;
});

// Route get all users
$route->get('/api/users/all/{token}', function ($request, $response, $args) {
  
  $user = new USER();
  $result = $user->getAllUsers($args['token']);
  echo $result;

});

// Route get particular user
$route->get('/api/users/{id}/{token}', function($request, $response, $args){

  $user = new USER();
  $result = $user->getParticularUser((int)$args['id'], $args['token']);
  echo $result;

});

// Route delete user;
$route->get("/api/users/delete/{id}/{token}", function($request, $response, $args){
  $user = new USER();
  $user->setid((int)$args['id']);
  $result = $user->deleteUser($args['token']);
  
  echo $result;
});

$route->run();