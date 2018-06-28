<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// GET USER INFO
$app->get('/user/{id}',function(Request $request, Response $response, array $args) {

    if($request->getAttribute("jwt")['isAdmin'] != 1){
        if ($request->getAttribute("jwt")['id'] != $args['id']) {
            $error = ['error' => ['text' => 'Permission denied']];
            return $response->withJson($error);
        }
    }

    // Input validation
    if(empty($args['id'])) {
        $error = ['error' => ['text' => 'id cannot be empty']];
        return $response->withJson($error);
    }

    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture` FROM `users` WHERE id=:id OR `tec_regno`=:id";

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

/**
 * Search user by query
 * @param query search query
 */
$app->get('/user/search/{query}',function(Request $request, Response $response, array $args) {

    if($request->getAttribute("jwt")['isAdmin'] != 1){
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    // Input validation
    if(empty($args['query'])) {
        $error = ['error' => ['text' => 'Query cannot be empty']];
        return $response->withJson($error);
    }

    if(strlen($args['query']) < 3) {
        $error = ['error' => ['text' => 'Query must contain at least 3 characters']];
        return $response->withJson($error);
    }

    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture` FROM `users` 
            WHERE `name` LIKE :sq OR `email` LIKE :sq OR `nickname` LIKE :sq OR `line_id` LIKE :sq OR `instagram` LIKE :sq OR `mobile` LIKE :sq OR `tec_regno` LIKE :sq OR `address` LIKE :sq OR `NIM` LIKE :sq";

    try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':sq' => '%'.$args['query'].'%'
        ]);
        $user = $stmt->fetchAll(PDO::FETCH_OBJ);
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

// UPDATE USER INFO
$app->put('/user/{id}',function(Request $request, Response $response, array $args) {

    if ($request->getAttribute("jwt")['id'] != $args['id']) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $interests = $request->getParam('interests');
    $nickname = $request->getParam('nickname');
    $about_me = $request->getParam('about_me');
    $line_id = $request->getParam('line_id');
    $instagram = $request->getParam('instagram');
    $mobile = $request->getParam('mobile');
    $address = $request->getParam('address');

    if(!isset($name, $email, $interests, $nickname, $about_me, $line_id, $instagram, $mobile, $address)) {
      $error = ['error' => ['text' => 'Please fill in all fields']];
      return $response->withJson($error);
    }

    $sql = "UPDATE `users` SET 
            `name` = :name,
            `email` = :email,
            `interests` = :interests,
            `nickname` = :nickname,
            `about_me` = :about_me,
            `line_id` = :line_id,
            `instagram` = :instagram,
            `mobile` = :mobile,
            `address` = :address
             WHERE id=:id";

    try {
     $db = $this->get('db');
     $stmt = $db->prepare($sql);
     $stmt->execute([
       ':id' => $args['id'],
       ':name' => $name,
       ':email' => $email,
       ':interests' => $interests,
       ':nickname' => $nickname,
       ':about_me' => $about_me,
       ':line_id' => $line_id,
       ':instagram' => $instagram,
       ':mobile' => $mobile,
       ':address' => $address
     ]);
     $rowCount = $stmt->rowCount();
     if ($rowCount == 0) {
       $error = ['error' => ['text' => 'Error, nothing updated.']];
       return $response->withJson($error);
     }
     $result = ["notice"=>["type"=>"success", "text" => "User profile updated"]];
     return $response->withJson($result);
    }
    catch (PDOException $e) {
     $error = ['error' => ['text' => $e->getMessage()]];
     return $response->withJson($error);
    }
});

$app->post('/useCoupon', function(Request $request, Response $response, array $args) {
  $coupon = $request->getParam('coupon');
  $id = $request->getAttribute("jwt")['id'];

  $sql = "SELECT EXISTS(SELECT * from coupons where coupon = :coupon) as ada_kupon";


  $db = $this->get('db');
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([
      ':coupon' => $coupon
    ]);
    $result = $stmt->fetch();
    
    if($result['ada_kupon'] != 1) {
      return $response->withJson(['error'=>['text' => 'Invalid coupon']]);
    }
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  try {
    $db->beginTransaction();
    $stmt = $db->prepare("UPDATE `users` SET `lunas` = 1 WHERE `id`=:id");
    $stmt->execute([':id' => $id]);

    $stmt = $db->prepare("DELETE FROM `coupons` WHERE `coupon`=:coupon");
    $stmt->execute([':coupon' => $coupon]);

    $db->commit();

    $result = ["notice"=>["type"=>"success", "text" => "Coupon use successful. Status: lunas"]];
    return $response->withJson($result);
  }
  catch (PDOException $e) {
    $db->rollBack();
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }



});