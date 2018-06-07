<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// GET ALL USERS
$app->get('/users', function(Request $request, Response $response, array $args) {
  $sql = "SELECT id, name, email, created_at, updated_at, lunas, isAdmin FROM users";
  try {
    $db = $this->get('db');
    $stmt = $db->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    return $response->withJson($users);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

// GET USER INFO
$app->get('/user/{id}',function(Request $request, Response $response, array $args) {
    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin` FROM `users` WHERE id=:id";

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

// GET USER RESULT IN A QUIZ
$app->get('/user/{uid}/quiz/{qid}', function(Request $request, Response $response, array $args) {
  $sql = "SELECT user_answer.answer, user_score.score as quiz_score, quiz.title as quiz_title, question, question_answer.type as question_type, question_answer.answer as correct_answer, decoy FROM user_answer INNER JOIN question_answer ON user_answer.qa_id = question_answer.id INNER JOIN users ON user_answer.user_id = users.id INNER JOIN quiz ON quiz.id = question_answer.quiz_id INNER JOIN user_score on user_score.quiz_id = quiz.id WHERE quiz.id = :qid AND users.id = :uid";
 try {
   $db = $this->get('db');

   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':uid' => $args['uid'],
     ':qid' => $args['qid']
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