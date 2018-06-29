<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// CREATE AN ASSIGNMENT
$app->post('/assignment', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $title = $request->getParam('title');
 $description = $request->getParam('description');

 $sql = "INSERT INTO `assignments`(`title`,`description`) VALUES (:title, :description)";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':title' => $title,
     ':description' => $description
   ]);

   $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully added"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// EDIT AN ASSIGNMENT
$app->put('/assignment/{id}', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $title = $request->getParam('title');
 $description = $request->getParam('description');
 $id = $args['id'];

 $sql = "UPDATE `assignments` SET `title` = :title, `description` = :description WHERE `id` = :id";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':title' => $title,
     ':description' => $description,
     ':id' => $id
   ]);

   $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully updated"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// DELETE AN ASSIGNMENT
$app->delete('/assignment/{id}', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $id = $args['id'];

 $sql = "DELETE FROM `assignments` WHERE `id` = :id";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $id
   ]);

   $data = ["notice"=>["type"=>"success", "text" => "Assignment sucessfully deleted"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// GET ALL ASSIGNMENT

$app->get('/assignment', function(Request $request, Response $response, array $args) {

 $sql = "SELECT `id`,`title`,`description` FROM `assignments`";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $id
   ]);

   $assignment = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($assignment);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});

// GET ASSIGNMENT

$app->get('/assignment/{id}', function(Request $request, Response $response, array $args) {

 $id = $args['id'];

 $sql = "SELECT `id`,`title`,`description` FROM `assignments` WHERE `id` = :id";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $id
   ]);

   $assignment = $stmt->fetch(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($assignment);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

});