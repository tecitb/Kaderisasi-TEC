<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
        'name' => $user->name,
        'isAdmin' => $user->isAdmin,
        'tec_regno' => $user->tec_regno
    ], $settings['jwt']['secret'], "HS256");

    return $this->response->withJson(['token' => $token,'id' => $user->id]);
});

$app->get('/verifyToken/{uid:[0-9]+}', function (Request $request, Response $response, array $args) {

    $uid = $args["uid"];
    $tokenUid = $request->getAttribute("jwt")['id'];

    if ($uid == $tokenUid){
        return $this->response->withJson(['status' => "valid","isAdmin" => $request->getAttribute("jwt")['isAdmin']]);
    }else {
        return $this->response->withJson(['status' => "invalid"]);
    }
});


// REGISTRATION
$app->post('/registration', function(Request $request, Response $response, array $args) {

    $name = $request->getParam('name');
    $email = $request->getParam('email');
    $nim = $request->getParam('nim');

    if(filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
        $error = ['error' => ['text' => "$email is not a valid email address"]];
        return $response->withJson($error);
    }
    $password = password_hash($request->getParam('password'), PASSWORD_DEFAULT);
    $created_at = date("Y-m-d H:i:s");
    $lunas = 0;

    $deleteCoupon = false;

    if ($request->getParam('coupon')) {
        $coupon = $request->getParam('coupon');
        $sql = "SELECT * from coupons where coupon = :coupon";

        try {
            $db = $this->get('db');
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':coupon' => $coupon
            ]);
            $result = $stmt->fetch();

            if($stmt->rowCount() > 0) {
                $lunas = $result['lunas'];
                $deleteCoupon = true;
            }
            else {
                return $response->withJson(['error'=>['text' => 'Invalid coupon']]);
            }
        }
        catch (PDOException $e) {
            $error = ['error' => ['text' => $e->getMessage()]];
            return $response->withJson($error);
        }

    }

    $verified = md5(uniqid(rand(),true));
    $isAdmin = 0;

    $sql = "INSERT INTO `users`
          (`name`, `email`, `password`, `nim`, `created_at`, `lunas`, `verified`, `isAdmin`, `interests`, `nickname`, `about_me`, `line_id`, `instagram`, `mobile`, `tec_regno`, `address`)
          VALUES (:name,:email,:password,:nim,:created_at,:lunas,:verified, :isAdmin, :interests, :nickname, :about_me, :line_id, :instagram, :mobile, :tec_regno, :address)";

    /* Informational fields */
    $interests = $request->getParam("interests", '');
    $nickname = $request->getParam("nickname", '');
    $aboutMe = $request->getParam("about_me", '');
    $lineId = $request->getParam("line_id", '');
    $instagram = $request->getParam("instagram", '');
    $mobile = $request->getParam("mobile", '');
    $address = $request->getParam("address", '');

    /* Generate TEC registration number */
    $tecRegNo = 1;
    $sq2 = "SELECT `tec_regno` FROM `users` WHERE `tec_regno` REGEXP '^TEC[0-9]' ORDER BY `tec_regno` DESC";

    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sq2);
        $stmt->execute();
        $result = $stmt->fetch();

        if($stmt->rowCount() > 0) {
            $tecRegNo = substr($result['tec_regno'],3)+1;
        }
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }

    $tecRegNoStr = "TEC".str_pad($tecRegNo, 3, '0', STR_PAD_LEFT);
    try {
        $db = $this->get('db');
        $db->beginTransaction();

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':nim' => $nim,
            ':created_at' => $created_at,
            ':lunas' => $lunas,
            ':verified' => $verified,
            ':isAdmin' => $isAdmin,
            ':interests' => $interests,
            ':nickname' => $nickname,
            ':about_me' => $aboutMe,
            ':line_id' => $lineId,
            ':instagram' => $instagram,
            ':mobile' => $mobile,
            ':tec_regno' => $tecRegNoStr,
            ':address' => $address
        ]);

        $user_id = $db->lastInsertId();

        if($deleteCoupon) {
            $delcouponsql = "DELETE FROM coupons WHERE coupon = :coupon";
            $stmt = $db->prepare($delcouponsql);
            $stmt->execute([
                ':coupon' => $request->getParam('coupon')
            ]);
        }
        $db->commit();

        $settings = $this->get('settings'); // get settings array.
        $token = JWT::encode([
            'id' => $user_id,
            'tec_regno' => $tecRegNoStr,
            'name' => $name,
            'email' => $email,
            'isAdmin' => $isAdmin
        ], $settings['jwt']['secret'], "HS256");

        return $this->response->withJson(['token' => $token,'id' => $user_id]);
    }
    catch (PDOException $e) {
        $db->rollBack();
        $msg = $e->getMessage();
        if (strpos($e->getMessage(), 'Duplicate entry') !== FALSE) {
            $msg = "User already exists";
        }
        $error = ['error' => ['text' => $msg]];
        return $response->withJson($error);
    }

});

// User reset
$app->post('/reset', function(Request $request, Response $response, array $args) {

    $email = $request->getParam('email');

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
        $error = ['error' => ['text' => 'Invalid email address']];
        return $response->withJson($error);
    }

    try {
        $token = md5(uniqid(rand(),true));

        $db = $this->get('db');
        $stmt = $db->prepare("SELECT id, email, name FROM users WHERE email = :email");
        $stmt->execute([
            ':email' => $email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($row['email'])){
            $error = ['error' => ['text' => 'User not found']];
            return $response->withJson($error);
        }

        $id = $row['id'];

        $stmt = $db->prepare("SELECT * FROM `user_reset` WHERE `user_id`=:id");
        $stmt->execute(array(
            ':id' => $id
        ));

        if($stmt->rowCount() == 0) {
            $stmt = $db->prepare("INSERT INTO `user_reset`(`user_id`, `resetToken`) VALUES (:id,:token)");
        } else {
            $stmt = $db->prepare("UPDATE `user_reset` SET`resetToken`=:token WHERE `user_id`=:id");
        }

        $stmt->execute(array(
            ':id' => $id,
            ':token' => $token
        ));

        $UI_BASE_URL = getenv("UI_BASE_URL") ?: 'http://localhost';
        $link = $UI_BASE_URL."/reset/$token";

        /** @var PHPMailer $mail */
        $mail = $this->get('mailer');

        $mail->addAddress($row['email'], $row['name']);
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'TEC Internship 2018 - Lupa kata sandi';
        $mail->Body    = '<p>Halo, '.$row['name'].'!</p><p>Kami baru saja menerima permintaan untuk me-reset kata sandi kamu. Buka tautan <a href="'.$link.'">'.$link.'</a> untuk me-reset kata sandi kamu.</p><br /><p>--<br />Sang Robot TEC<br />Tim Pengembangan IT<br />Techno Entrepreneur Club</p>';

        $mail->send();

        $result = ["notice"=>["type"=>"success", "text" => "Check email to reset password"]];

        return $response->withJson($result);
    }
    catch (PDOException $e) {
        $msg = $e->getMessage();
        $error = ['error' => ['text' => $msg]];
        return $response->withJson($error);
    }
});

$app->get('/reset/{token}', function(Request $request, Response $response, array $args) {
    $token = $args["token"];
    $sql = "SELECT `user_id`, `resetToken` as `reset_token` FROM `user_reset` WHERE resetToken = :token";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':token' => $token
        ]);
        $result = $stmt->fetch(PDO::FETCH_OBJ);

        return $response->withJson($result);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

$app->put('/reset/{token}', function(Request $request, Response $response, array $args) {
    $user_id = $request->getParam("user_id");
    $token = $request->getParam("reset_token");
    $new_password = $request->getParam("new_password");
    $sql = "SELECT `user_id` FROM `user_reset` WHERE resetToken = :token AND user_id = :uid";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':token' => $token,
            ':uid' => $user_id
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if(empty($row['user_id'])){
            $error = ['error' => ['text' => 'Forbidden']];
            return $response->withJson($error);
        }

        $password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE `users` SET password = :password WHERE id = :uid");
        $stmt->execute([
            ':password' => $password,
            ':uid' => $user_id
        ]);

        $result = ["notice"=>["type"=>"success", "text" => "Password reset success"]];
        return $response->withJson($result);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Redirects to user display picture
 */
$app->get("/dp/{id}",  function(Request $request, Response $response, array $args) {
    // Input validation
    if(empty($args['id'])) {
        return $response->withStatus(400);
    }

    $sql = "SELECT `profile_picture_url` FROM `users` WHERE id=:id";

    try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $args['id']
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if($user == false) {
            return $response->withStatus(404);
        }

        if(empty($user->profile_picture_url)) {
            return $response->withStatus(404);
        } else {
            return $response->withRedirect($user->profile_picture_url);
        }
    }
    catch (PDOException $e) {
        return $response->withStatus(500);
    }
});


// Get all groups name and id
$app->get('/groups', function(Request $request, Response $response, array $args) {
  $sql = "SELECT * FROM `groups`";

  $db = $this->get('db');
  try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
  }
  catch (PDOException $e) {
    $error = ['error' => ['text' => $e->getMessage()]];
    return $response->withJson($error);
  }

  return $response->withJson($result);

});
