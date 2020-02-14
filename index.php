<?php
// Test branch gleyson
require "vendor/autoload.php";

use \GLEYSON\Model\User as USER;

$route = new \Slim\Slim();

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

// Route add particular user
$route->post("/api/users/add/:token",
function($token){

  $user = new USER();
  $user->setData($_POST);
  $result = $user->addUser($token);
  
  echo $result;

});

// Route delete user;
$route->delete("/api/users/delete/:id/:token", function($id, $token){
  $user = new USER();
  $user->setid((int)$id);
  $result = $user->deleteUser($token);
  
  echo $result;
});

// Route update user;
$route->post("/api/users/update/:id/:token", function($id, $token){
  
  $user = new USER();
  $user->setData($_POST);
  $result = $user->updateUser((int)$id, $token);
  
  echo $result;
});

$route->run();