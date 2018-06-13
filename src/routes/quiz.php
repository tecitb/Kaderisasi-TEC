<?php 

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;


// GET A QUIZ DETAILS
$app->get('/quiz/{id}', function(Request $request, Response $response, array $args) {
	$sql = "SELECT title, question_answer.id, `type`, `question`, `answer`, `decoy`, `created_at` FROM `question_answer` INNER JOIN quiz ON question_answer.quiz_id = quiz.id WHERE quiz.id = :id";

   try {
     $db = $this->get('db');

     $stmt = $db->prepare($sql);
     $stmt->execute([
       ':id' => $args['id']
     ]);
     $result = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
     foreach ($result as $quiz) {
      if($quiz->type == "pilgan") {
        $quiz->option = array_merge(explode(", ", $quiz->decoy), [$quiz->answer]);
        shuffle($quiz->option);
        unset($quiz->answer);
        unset($quiz->decoy);
      }
      elseif ($quiz->type == "isian") {
        unset($quiz->answer);
        unset($quiz->decoy);
      }
     }
     return $response->withJson($result);
   }
   catch (PDOException $e) {
     $error = ['error' => ['text' => $e->getMessage()]];
     return $response->withJson($error);
   }
});

// GET ALL QUIZ
$app->get('/quiz', function(Request $request, Response $response, array $args) {
	$sql = "SELECT * FROM `quiz`";
   try {
     $db = $this->get('db');

     $stmt = $db->query($sql);
     $quiz = $stmt->fetchAll(PDO::FETCH_OBJ);
     $db = null;
     return $response->withJson($quiz);
   }
   catch (PDOException $e) {
     $error = ['error' => ['text' => $e->getMessage()]];
     return $response->withJson($error);
   }
});


// Kirim jawaban user untuk diproses
$app->post('/answer', function(Request $request, Response $response, array $args) {

  $answers = $request->getParam('answers');
  $quiz_id = $request->getParam('quiz_id');
  $user_id = $request->getAttribute("jwt")['id'];

  if (filter_var($quiz_id, FILTER_VALIDATE_INT) === FALSE) {
   $error = ['error' => ['text' => 'Invalid quiz id']];
   return $response->withJson($error);
  }

  // Cek apakah user sudah pernah jawab

  $sql = "SELECT EXISTS(SELECT * from user_score where user_id = :user_id) as sudah_jawab";

  try {
    $db = $this->get('db');
    $stmt = $db->prepare($sql);
    $stmt->execute([':user_id' => $user_id]);
    $result = $stmt->fetch();
    
    if($result['sudah_jawab'] == 1) {
      return $response->withJson(['error'=>['text' => 'Kuis hanya dapat dijawab sekali']]);
    }
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  // User belum pernah jawab, lanjutkan...

  $sql = "INSERT INTO user_answer(`answer`,`qa_id`, `user_id`) VALUES ";

  $placeholders = [];
  $user_answer = [];
  $sql_add = [];
  foreach ($answers as $answer) {
   $sql_add[] = "(?, ?, " . $user_id . ")";
   $placeholders[] = $answer["answer"];
   $placeholders[] = $answer["qa_id"];
   $user_answer[$answer["qa_id"]] = $answer["answer"];
  }

  $sql .= implode(",", $sql_add);

  try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute($placeholders);
  }
  catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
  }

  $question_marks = array_map(function($element) {
   return '?';
  }, $user_answer);

  $sql = "SELECT `id` as `qa_id`, `answer` as `correct_answer` FROM `question_answer` WHERE `id` IN (" . implode(',', $question_marks) . ") AND quiz_id = " . $quiz_id;

  try {
   $db = $this->get('db');
   $stmt = $db->prepare($sql);
   $stmt->execute(array_keys($user_answer));
   $results = $stmt->fetchAll();

   $score = 0;
   $max = 0;

   foreach ($results as $result) {
     $key = $result['qa_id'];
     if($user_answer[$key] == $result['correct_answer']) {
       $score++;
     }
     $max++;
   }

   $grade = $score * 100 / $max;

   $sql = "INSERT INTO `user_score`(`score`, `quiz_id`, `user_id`) VALUES (:grade,:quiz_id,:user_id)";
   $stmt = $db->prepare($sql);
   $stmt->execute([
     ':grade' => $grade,
     ':quiz_id' => $quiz_id,
     ':user_id' => $user_id
   ]);

   $data = ["notice"=>["type"=>"success", "text" => "Answer successfully submitted"]];
   return $response->withJson($data);
  }
  catch (PDOException $e) {
   $error = ['error' => ['text' => $e->getMessage()]];
   return $response->withJson($error);
  }
});