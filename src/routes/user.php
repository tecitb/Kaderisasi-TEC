<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use Slim\Http\UploadedFile;
use Aws\S3\S3Client;

// GET USER INFO
$app->get('/user/{id:[0-9]+}',function(Request $request, Response $response, array $args) {
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

    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`, `profile_picture_url`, `is_active`,`gid` FROM `users` WHERE id=:id";

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

// GET USER INFO
$app->get('/user/{id:[0-9]+}/group',function(Request $request, Response $response, array $args) {
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

    $sql = "SELECT * FROM user_group WHERE uid=:uid";

    try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':uid' => $args['id']
        ]);
        $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $db = null;
        return $response->withJson($groups);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

// GET USER INFO by TEC REG NO
$app->get('/user/regno/{id}',function(Request $request, Response $response, array $args) {
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

    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`,`is_active`,`gid` FROM `users` WHERE tec_regno=:id";

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

    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`, `profile_picture_url`, `is_active`,`gid` FROM `users`
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
$app->get('/user/{id:[0-9]+}/score', function(Request $request, Response $response, array $args) {
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

// CHANGE PASSWORD
$app->post('/change-password', function(Request $request, Response $response, array $args) {
  $db = $this->get('db');
  $sql = "SELECT password FROM users WHERE id = :id";
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([
      ':id' => $request->getAttribute("jwt")['id']
    ]);
    $result = $stmt->fetch();
    if (!password_verify($request->getParam('old_password'),$result['password'])) {
        return $response->withJson(['error' => ['text' => 'Invalid old password']]);
    }
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  $sql = "UPDATE users SET password = :password WHERE id = :id";
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([
      ':password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT),
      ':id' => $request->getAttribute("jwt")['id']
    ]);
    $rowCount = $stmt->rowCount();
    if ($rowCount == 0) {
      $error = ['error' => ['text' => 'Change password failed.']];
      return $response->withJson($error);
    }
    $result = ["notice"=>["type"=>"success", "text" => "Password successfully changed"]];
    return $response->withJson($result);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }
});

$app->post('/uploadImage', function(Request $request, Response $response, array $args) {
    set_time_limit(0);
    $userId = $request->getAttribute("jwt")['id'];
    try {
        // Try to find user
        $sql = "SELECT * FROM `users` WHERE `id`=:id";
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $userId
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if($user == false) {
            $error = ['error' => ['text' => 'User not found']];
            return $response->withJson($error);
        }

        $uploadedFiles = $request->getUploadedFiles();

        $uploadedFile = $uploadedFiles['profile_picture'];
        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            // Try to save image
            $uploadedFiles = $request->getUploadedFiles();

            if(array_key_exists("profile_picture", $uploadedFiles)) {
                /** @var UploadedFile $uploadedFile */
                $uploadedFile = $uploadedFiles['profile_picture'];
                if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                    /** @var Aws\S3\S3Client $spaces */
                    $spaces = $this->spaces;

                    $extension = "jpg"; //pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                    $basename = bin2hex(random_bytes(8));
                    $filename = 'userpic/user_' . $userId . '_' . sprintf('%s.%0.8s', $basename, $extension);

                    if(!empty($user->profile_picture)) {
                        $spaces->deleteObject([
                            'Bucket' => $this->get('settings')['spaces']['name'],
                            'Key' => $user->profile_picture
                        ]);
                    }

                    // Upload a file to the Space
                    // Try to compress the image
                    $img = imagecreatefromstring($uploadedFile->getStream()->getContents());

                    ob_start();
                    imagejpeg($img, null, 50);
                    $data = ob_get_clean();

                    /** @var \Aws\Result $insert */
                    $insert = $spaces->putObject([
                        'Bucket' => $this->get('settings')['spaces']['name'],
                        'Key'    => $filename,
                        'ACL'    => 'public-read',
                        'Body'   => $data,
                        'ContentType' => "image/jpeg"
                    ]);

                    $objectUrl = $insert->get("ObjectURL");

                    $file_dbentry = $objectUrl;
                } else {
                    $file_dbentry = $user->profile_picture_url;
                    $filename = $user->profile_picture_url;
                }
            } else {
                $file_dbentry = $user->profile_picture_url;
                $filename = $user->profile_picture_url;
            }

            //$uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

            $sql = "UPDATE users SET profile_picture=:pp, profile_picture_url=:pp_url WHERE id = :id";

              $db = $this->get('db');
              $stmt = $db->prepare($sql);
              $stmt->execute([
                ':pp' => $filename,
                ':pp_url' => $file_dbentry,
                ':id' => $userId
              ]);
              $rowCount = $stmt->rowCount();
              if ($rowCount == 0) {
                $error = ['error' => ['text' => 'Error, nothing updated.']];
                return $response->withJson($error);
              }
              $result = ["notice"=>["type"=>"success", "text" => "Profile picture updated"], "filename" => $filename, "url" => $file_dbentry];
              return $response->withJson($result);
        }
        else {
          return $response->withJson(['error'=>['text' => 'Upload failed']]);
        }
    } catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});


// UPDATE USER INFO
$app->put('/user/{id:[0-9]+}',function(Request $request, Response $response, array $args) {

    if ($request->getAttribute("jwt")['id'] != $args['id']) {
      if ($request->getAttribute("jwt")['isAdmin'] != 1) {
         $error = ['error' => ['text' => 'Permission denied']];
         return $response->withJson($error);
       }
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
    $isActive = $request->getParam('is_active');

    if(!isset($name, $email, $interests, $nickname, $about_me, $line_id, $instagram, $mobile, $address, $isActive)) {
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
            `address` = :address,
            `is_active` = :is_active
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
       ':address' => $address,
       ':is_active' => ($isActive == 1) ? 1 : 0
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

// USER PAKAI COUPON
$app->post('/useCoupon', function(Request $request, Response $response, array $args) {
  $coupon = $request->getParam('coupon');
  $id = $request->getAttribute("jwt")['id'];

  $sql = "SELECT * from coupons where coupon = :coupon";

  try {
    $db = $this->get('db');
    $stmt = $db->prepare($sql);
    $stmt->execute([
      ':coupon' => $coupon
    ]);
    $result = $stmt->fetch();

    if($stmt->rowCount() == 0 OR $result['lunas'] == 0) {
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

$app->get('/group/{gid:[0-9]+}', function(Request $request, Response $response, array $args) {
  $sql = "SELECT * FROM `groups` WHERE `id`=:gid";

  $db = $this->get('db');
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([":gid" => $args["gid"]]);
    $result = $stmt->fetch();
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  return $response->withJson($result);

});

$app->get('/group/{gid:[0-9]+}/members', function(Request $request, Response $response, array $args) {
  $sql = "SELECT * FROM `groups` WHERE `id`=:gid";

  $db = $this->get('db');
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([":gid" => $args["gid"]]);
    $result = $stmt->fetch();
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  if($result["head"] != $request->getAttribute("jwt")["id"]){
    $error = ['error' => ['text' => 'Not head of group']];
    return $response->withJson($error);
  }

  $sql = "SELECT `id`,`NIM`,`tec_regno`,`name`,`profile_picture` FROM `users` INNER JOIN `user_group` ON `uid`=`id` WHERE user_group.gid=:gid";

  try {
    $stmt = $db->prepare($sql);
    $stmt->execute([":gid" => $args["gid"]]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  return $response->withJson($result);

});
