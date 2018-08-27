<?php
use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;
use Slim\Http\UploadedFile;
use Aws\S3\S3Client;

// GET USER INFO
$app->get('/linegrouprlinegrouprediredir',function(Request $request, Response $response, array $args) {
    $sql = "SELECT `id`,`name`,`email`,`created_at`,`updated_at`,`lunas`,`verified`,`isAdmin`,`interests`,`nickname`,`about_me`,`line_id`,`instagram`,`mobile`,`tec_regno`,`address`, `NIM`, `profile_picture`, `profile_picture_url`, `is_active`,`gid` FROM `users` WHERE id=:id";

    try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $request->getAttribute("jwt")['id']
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        $active = $user->is_active;

        if($active != 1) {
            $error = ['error' => ['text' => "Inactive user"]];
            return $response->withJson($error);
        }

        // All is well, return link
        if(is_numeric(substr($user->tec_regno, -1))) {
            if (substr($user->tec_regno, -1) % 2 == 1) {
                // Is odd
                return $response->withJson(["link" => getenv("LINE_GROUP_INTERNS_ODD") ?: BASE_URL]);
            } else {
                // Is even or invalid
                return $response->withJson(["link" => getenv("LINE_GROUP_INTERNS_EVEN") ?: BASE_URL]);
            }
        } else {
            return $response->withJson(["link" => getenv("LINE_GROUP_INTERNS_ODD") ?: BASE_URL]);
        }
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});