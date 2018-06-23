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
    $sql = "SELECT `id`,`name`,`email`,`nim`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address` FROM `users` WHERE id=:id";

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

$app->post('/uploadImage', function(Request $request, Response $response, array $args) {
    $directory = $this->get('settings')['upload_directory'];

    $uploadedFiles = $request->getUploadedFiles();

    $uploadedFile = $uploadedFiles['profile_picture'];
    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
        $user_id = $request->getAttribute("jwt")['id'];
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); 
        $filename = 'user_' . $user_id . '_' . sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        $sql = "UPDATE users SET profile_picture=:pp WHERE id = :id";

        try {
          $db = $this->get('db');
          $stmt = $db->prepare($sql);
          $stmt->execute([
            ':pp' => $filename,
            ':id' => $user_id
          ]);
          $rowCount = $stmt->rowCount();
          if ($rowCount == 0) {
            $error = ['error' => ['text' => 'Error, nothing updated.']];
            return $response->withJson($error);
          }
          $result = ["notice"=>["type"=>"success", "text" => "Profile picture updated"], "filename" => $filename];
          return $response->withJson($result);
        }
        catch (PDOException $e) {
          $error = ['error' => ['text' => $e->getMessage()]];
          return $response->withJson($error);
        }
    }
    else {
      return $response->withJson(['error'=>['text' => 'Upload failed']]);
    }

});