<?php
/**
 * Remote Data Access endpoint
 * This endpoint allows third-party data access using Data Access Code
 */

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Get user Data Access Code
 */
$app->get('/rda/dac',function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $q = "SELECT * FROM `dac` WHERE `user_id`=:user_id";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $dac = $stmt->fetch(PDO::FETCH_OBJ);

        if($dac === false) return $response->withJson(["error" => ["text" => "Data Access Code is not generated", "code" => 404]]);

        // Get last used by user info
        if(!empty($dac->last_used_by)) {
            $qf = "SELECT `name` FROM `users` WHERE `id`=:user_id";
            $stmt = $db->prepare($qf);
            $stmt->execute([
                ':user_id' => $dac->last_used_by
            ]);

            $usr = $stmt->fetch(PDO::FETCH_OBJ);
            if($usr !== false) $dac->last_used_by_name = $usr->name;
        }

        $db = null;
        return $response->withJson($dac);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Generate user Data Access Code
 */
$app->post('/rda/dac',function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $q = "SELECT * FROM `dac` WHERE `user_id`=:user_id";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $dac = str_pad($userId, 3, '0', STR_PAD_LEFT).strtoupper(generateRandomString(6));

        if($stmt->rowCount() > 0) {
            // Data exists
            $q = "UPDATE `dac` SET `dac`=:dac WHERE `user_id`=:user_id";
        } else {
            $q = "INSERT INTO `dac` (`user_id`, `dac`) VALUES (:user_id, :dac)";
        }

        $stmt = $db->prepare($q);
        $stmt->execute([
            ':user_id' => $userId,
            ':dac' => $dac
        ]);

        return $response->withJson(['success' => true, 'dac' => $dac]);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Get user data from Data Access Code
 */
$app->get('/rda/user',function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];
    $dac = $request->getParam("dac");

    $q = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`, `profile_picture_url`, `is_active`,`gid` 
          FROM `users` 
          LEFT JOIN `dac` ON `users`.`id` = `dac`.`user_id`
          WHERE `dac`.`dac`=:dac";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':dac' => $dac
        ]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if($user === false) return $response->withJson(["error" => ["text" => "Data Access Code is invalid", "code" => 400]]);

        // Update RDA record
        $q = "UPDATE `dac` SET `last_used`=:last_used, `last_used_by`=:user_id, `used` = `used` + 1 WHERE `dac`=:dac";
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':dac' => $dac,
            ':last_used' => time(),
            ':user_id' => $userId
        ]);

        $db = null;

        return $response->withJson($user);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});


/**
 * Get user data from Data Access Code
 */
$app->get('/rda/vcard',function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];
    $dac = $request->getParam("dac");

    $q = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`, `profile_picture_url`, `is_active`,`gid` 
          FROM `users` 
          LEFT JOIN `dac` ON `users`.`id` = `dac`.`user_id`
          WHERE `dac`.`dac`=:dac";
    try {
        $db = $this->get('db');
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':dac' => $dac
        ]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if($user === false) return $response->withJson(["error" => ["text" => "Data Access Code is invalid", "code" => 400]]);

        // Update RDA record
        $q = "UPDATE `dac` SET `last_used`=:last_used, `last_used_by`=:user_id, `used` = `used` + 1 WHERE `dac`=:dac";
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':dac' => $dac,
            ':last_used' => time(),
            ':user_id' => $userId
        ]);

        $db = null;

        $vcard = "BEGIN:VCARD\r\n";
        if($user->name != '') $vcard .= "FN:".$user->name."\r\n";
        $vcard .= "ORG:Techno Entrepreneur Club ITB\r\n";
        if($user->mobile != '') $vcard .= "TEL;CELL:".$user->mobile."\r\n";
        if($user->address != '') $vcard .= "ADR;HOME:".$user->address."\r\n";
        if($user->tec_regno != '') $vcard .= "UID:".$user->tec_regno."\r\n";
        if($user->email != '') $vcard .= "EMAIL:".$user->email."\r\n";
        if($user->about_me != '') $vcard .= "NOTE:".$user->about_me."\r\n";
        if($user->line_id != '') $vcard .= "X-LINE:".$user->line_id."\r\n";
        if($user->instagram != '') $vcard .= "X-INSTAGRAM:".$user->instagram."\r\n";
        if($user->interests != '') $vcard .= "X-INTERESTS:".$user->interests."\r\n";
        $vcard .= "PHOTO:".BASE_URL."/api/dp/".$user->id."\r\n";
        $vcard .= "END:VCARD";

        return $response->withJson(array("vcard" => utf8_encode($vcard)));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

function generateRandomString($length = 10) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}