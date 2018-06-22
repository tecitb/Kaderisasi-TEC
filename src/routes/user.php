<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// GET USER INFO
$app->get('/user/{id}',function(Request $request, Response $response, array $args) {

    if ($request->getAttribute("jwt")['id'] != $args['id']) {
      if($request->getAttribute("jwt")['isAdmin'] != 1){
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
      }
    }
    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address` FROM `users` WHERE id=:id";

    try {
      $db = $this->get('db');

      $stmt = $db->prepare($sql);
      $stmt->execute([
        ':id' => $args['id']
      ]);
      $user = $stmt->fetch(PDO::FETCH_OBJ);
      $db = null;
      return $response->withJson($user);
    }
    catch (PDOException $e) {
      $error = ['error' => ['text' => $e->getMessage()]];
      return $response->withJson($error);
    }
});


// GET ALL SCORE OF A USER
$app->get('/user/{id}/score', function(Request $request, Response $response, array $args) {
  $sql = "SELECT users.id as user_id, users.name, quiz.title, quiz.id as quiz_id, user_score.score as score FROM user_score INNER JOIN users ON user_score.user_id = users.id INNER JOIN quiz on user_score.quiz_id = quiz.id WHERE users.id = :id";
 try {
   $db = $this->get('db');

   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':id' => $args['id']
   ]);
   $result = $stmt->fetchAll(PDO::FETCH_OBJ);
   $db = null;
   return $response->withJson($result);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// VERIFY USER EMAIL
$app->get('/verify/{token}', function(Request $request, Response $response, array $args) {
  $sql = "UPDATE users SET verified = 'YES' WHERE verified = :token";
  try {
    $db = $this->get('db');
    $stmt = $db->prepare($sql);
    $stmt->execute([
      ':token' => $args['token']
    ]);
    $rowCount = $stmt->rowCount();
    if ($rowCount == 0) {
      $error = ['error' => ['text' => 'Invalid token.']];
      return $response->withJson($error);
    }
    $result = ["notice"=>["type"=>"success", "text" => "Verification successful"]];
    return $response->withJson($result);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});
