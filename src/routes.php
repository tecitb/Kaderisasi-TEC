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
 
    $app->get('/user',function(Request $request, Response $response, array $args) {
      return $this->response->withJson($request->getAttribute("jwt"));
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
        $quiz = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
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
        $quiz = $stmt->fetch(PDO::FETCH_OBJ);
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
        $result = $stmt->fetch(PDO::FETCH_OBJ);
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
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
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
      if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
      $resetToken = md5(uniqid(rand(),true));
      $isAdmin = 0;

      $sql = "INSERT INTO `users`(`name`, `email`, `password`, `created_at`, `lunas`, `verified`, `resetToken`, `isAdmin`) VALUES (:name,:email,:password,:created_at,:lunas,:verified, :resetToken, :isAdmin)";

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
          ':resetToken' => $resetToken,
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

});
