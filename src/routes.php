<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view

    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->post('/login', function (Request $request, Response $response, array $args) {
 
    $input = $request->getParsedBody();
    $sql = "SELECT * FROM users WHERE email= :email";
    $sth = $this->db->prepare($sql);
    $sth->bindParam("email", $input['email']);
    $sth->execute();
    $user = $sth->fetchObject();
 
    // verify email address.
    if(!$user) {
        return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']);
    }
 
    // verify password.
    if (!password_verify($input['password'],$user->password)) {
        return $this->response->withJson(['error' => true, 'message' => 'These credentials do not match our records.']); 
    }
 
    $settings = $this->get('settings'); // get settings array.
    $token = JWT::encode([
    	'id' => $user->id,
    	'email' => $user->email,
    	'isAdmin' => $user->isAdmin,
    	'exp' => time() + (3 * 24 * 60 * 60)
    ], $settings['jwt']['secret'], "HS256");
 
    return $this->response->withJson(['token' => $token]);
});


$app->group('/api', function(\Slim\App $app) {
 
    $app->get('/jwt',function(Request $request, Response $response, array $args) {
      return $this->response->withJson($request->getAttribute("jwt"));
    });

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

    $app->get('/user/{id}',function(Request $request, Response $response, array $args) {
        $sql = "SELECT * FROM `users` WHERE id=:id";

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
   	
   	$app->get('/quiz/{id}', function(Request $request, Response $response, array $args) {
   		$sql = "SELECT title, question_answer.id, `type`, `question`, `answer`, `decoy`, `created_at` FROM `question_answer` INNER JOIN quiz ON question_answer.quiz_id = quiz.id WHERE quiz.id = :id";

      try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
          ':id' => $args['id']
        ]);
        $quiz = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        $quiz->decoy = explode(", ", $quiz->decoy);
        return $response->withJson($quiz);
      }
      catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
      }
   	});

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

    $app->post('/user', function(Request $request, Response $response, array $args) {
      if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
      }

      $name = $request->getParam('name');
      $email = $request->getParam('email');
      if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
        $error = ['error' => ['text' => 'Not a valid email address']];
        return $response->withJson($error);
      }
      $password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
      $created_at = date("Y-m-d H:i:s");
      $lunas = 0;

      if ($request->getParam('coupon')) {
        $coupon = $request->getParam('coupon');
        $sql = "SELECT EXISTS(SELECT * from COUPONS where COUPON = :coupon) as ada_kupon";

        try {
          $db = $this->get('db');
          $stmt = $db->prepare($sql);
          $stmt->execute([
            ':coupon' => $coupon
          ]);
          $result = $stmt->fetch();
          
          if($result['ada_kupon'] == 1) {
            $lunas = 1;
          }
        }
        catch (PDOException $e) {
          $error = ['error' => ['text' => $e->getMessage()]];
          return $response->withJson($error);
        }

      }
      
      $verified = md5(uniqid(rand(),true));
      $isAdmin = 0;

      $sql = "INSERT INTO `users`(`name`, `email`, `password`, `created_at`, `lunas`, `verified`, `isAdmin`) VALUES (:name,:email,:password,:created_at,:lunas,:verified, :isAdmin)";

      try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
          ':name' => $name,
          ':email' => $email,
          ':password' => $password,
          ':created_at' => $created_at,
          ':lunas' => $lunas,
          ':verified' => $verified,
          ':isAdmin' => $isAdmin
        ]);

        if($lunas == 1) {
          $delcouponsql = "DELETE FROM coupons WHERE coupon = :coupon";
          $stmt = $db->prepare($delcouponsql);
          $stmt->execute([
            ':coupon' => $request->getParam('coupon')
          ]);
        }

        $data = ["notice"=>["type"=>"success", "text" => "User sucessfully added"]];
        return $response->withJson($data);
      }
      catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
      }
    });


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
      $type = $question_answer['type'];
      $question = $question_answer['question'];
      $answer = $question_answer['answer'];
      $decoy = implode(", ", $question_answer['decoy']);
      $created_at = date("Y-m-d H:i:s");
      $quiz_id = $db->lastInsertId();

      $sql = "INSERT INTO `question_answer`(`type`,`question`, `answer`, `decoy`, `created_at`, `quiz_id`) VALUES (:type, :question, :answer, :decoy, :created_at, :quiz_id)";
      try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
          ':type' => $type,
          ':question' => $question,
          ':answer' => $answer,
          ':decoy' => $decoy,
          ':created_at' => $created_at,
          ':quiz_id' => $quiz_id
        ]);

        $data = ["notice"=>["type"=>"success", "text" => "Quiz sucessfully added"]];
        return $response->withJson($data);
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
      if (filter_var($quiz_id, FILTER_VALIDATE_INT) === FALSE) {
        $error = ['error' => ['text' => 'Invalid quiz id']];
        return $response->withJson($error);
      }
      $user_id = $request->getAttribute("jwt")['id'];

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

      $sql .= implode(", ", $placeholders);

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
    })
});

$app->post('/reset', function(Request $request, Response $response, array $args) {

  $email = $request->getParam('email');

  if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
    $error = ['error' => ['text' => 'Invalid email address'];
    return $response->withJson($error);
  }
  
  try {
    $db = $this->get('db');
    $stmt = $db->prepare("SELECT id, email FROM users WHERE email = :email");
    $stmt->execute([
      ':email' => $email
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if(empty($row['email'])){
      $error = ['error' => ['text' => 'User not found'];
      return $response->withJson($error);
    }

    $id = $row['id'];
    $token = md5(uniqid(rand(),true));

    $stmt = $db->prepare("INSERT INTO `user_reset`(`user_id`, `resetToken`) VALUES (:id,:token)");
    $stmt->execute(array(
        ':id' => $id,
        ':token' => $token
    ));

    $to      = $email;
    $subject = 'Password Reset';
    $message = "<p>Someone requested that the password be reset.</p>
    <p>If this was a mistake, just ignore this email and nothing will happen.</p>
    <p>To reset your password, visit the following address: <a href=\"http://kaderisasi.tec.itb.ac.id/reset/$token\">http://kaderisasi.tec.itb.ac.id/reset/$token</a></p>";
    $headers = array(
        'From' => 'admin@kaderisasi.tec.itb.ac.id'
    );

    mail($to, $subject, $message, $headers);

    $result = ["notice"=>["type"=>"success", "text" => "Check email to reset password"]];
    return $response->withJson($result);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});