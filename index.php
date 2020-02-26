<?php
header("Access-Control-Allow-Origin: *");
require "vendor/autoload.php";

use \GLEYSON\Model\User as USER;

$route = new \Slim\Slim();

// Route add particular user
$route->post("/api/add/user/{token}",
function($request, $response, $args){

  $user = new USER();
  $user->setData($_POST);
  $result = $user->addUser($args['token']);
  
  echo $result;

});

// Route update user;
$route->post("/api/update/:id/:token", function($id, $token){
  
  $user = new USER();
  $user->setData($_POST);
  $result = $user->updateUser((int)$id, $token);
  
  echo $result;
});

// Route get all users
$route->get('/api/users/all/:token', function ($token) {
  $user = new USER();
  $result = $user->getAllUsers($token);
  echo $result;

});

// Route get particular user
$route->get('/api/users/:id/:token', function($id, $token){

  $user = new USER();
  $result = $user->getParticularUser((int)$id, $token);
  echo $result;

});

// Route delete user;
$route->get("/api/users/delete/:id/:token", function($id, $token){
  $user = new USER();
  $user->setid((int)$id);
  $result = $user->deleteUser($token);
  
  echo $result;
});

$route->run();