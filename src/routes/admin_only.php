<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// GET ALL USERS
$app->get('/users', function(Request $request, Response $response, array $args) {
  if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
  }

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

// GET USER RESULT IN A QUIZ
$app->get('/user/{uid}/quiz/{qid}', function(Request $request, Response $response, array $args) {
   if ($request->getAttribute("jwt")['isAdmin'] != 1) {
     $error = ['error' => ['text' => 'Permission denied']];
     return $response->withJson($error);
   }
   
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

// GENERATE COUPON
$app->post('/generateCoupon/{num}', function (Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $number_of_coupons = $args["num"];
 if (filter_var($number_of_coupons, FILTER_VALIDATE_INT) === FALSE) {
   $error = ['error' => ['text' => 'Invalid number of coupon']];
   return $response->withJson($error);
 }

 if($number_of_coupons > 50 || $number_of_coupons < 1) {
   $error = ['error' => ['text' => 'Enter number between 1 and 50 only']];
   return $response->withJson($error);
 }

 require_once dirname(__DIR__) . '/class.coupon.php';
 $sql = "INSERT INTO `coupons`(`coupon`) VALUES ";
 $pieces = [];
 for ($i=0; $i < $number_of_coupons; $i++) { 
   $pieces[] = "('" . coupon::generate(8) . "')";
 }
 $sql .= implode(',', $pieces);

 try {
   $db = $this->get('db');
   $stmt = $db->query($sql);
   $row_affected = $stmt->rowCount();
   if ($row_affected == $number_of_coupons) {
     $success = ["notice"=>["type"=>"success", "text" => "$row_affected coupons sucessfully added"]];
     return $response->withJson($success);
   }
   else {
     $warning = ["notice"=>["type"=>"warning", "text" => "Only $row_affected coupons sucessfully added"]];
     return $response->withJson($warning);
   }
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});

// CREATE A QUIZ
$app->post('/quiz', function(Request $request, Response $response, array $args) {
 if ($request->getAttribute("jwt")['isAdmin'] != 1) {
   $error = ['error' => ['text' => 'Permission denied']];
   return $response->withJson($error);
 }

 $title = $request->getParam('title');

 $sql = "INSERT INTO `quiz`(`title`) VALUES (:title)";
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':title' => $title
   ]);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }

 $question_answer = $request->getParam('question_answer');

 $data = [];
 foreach ($question_answer as $qa) {
   $data[] = $qa['type'];
   $data[] = $qa['question'];
   $data[] = $qa['answer'];
   $data[] = implode(", ", $qa['decoy']);
   $data[] = date("Y-m-d H:i:s");
   $data[] = $db->lastInsertId();
 }

 $count = count($data);
 $add = [];
 for ($i=0; $i < $count; $i = $i + 6) { 
   $add[] = "(?, ?, ?, ?, ?, ?)";
 }

 $sql = "INSERT INTO `question_answer`(`type`,`question`, `answer`, `decoy`, `created_at`, `quiz_id`) VALUES " . implode(',', $add);
 try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute($data);

   $data = ["notice"=>["type"=>"success", "text" => "Quiz sucessfully added"]];
   return $response->withJson($data);
 }
 catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
 }
});
