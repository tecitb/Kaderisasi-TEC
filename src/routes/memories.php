<?php

/**
 * TEC internApp Memories Backend
 * This route stores manages users' Memories records
 * @author Muhammad Aditya Hilmy
 */

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;

/**
 * Get Memories record belonging to a user for a specific Relation
 */
$app->get('/memories/get/{id}',  function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    // Input validation
    if(empty($args['id'])) {
        $error = ['error' => ['text' => 'id cannot be empty']];
        return $response->withJson($error);
    }

    try {
        // Try to find user
        $sql = "SELECT `id`,`name` FROM `users` WHERE tec_regno=:id";
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $args['id']
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if($user == false) {
            $error = ['error' => ['text' => "User does not exist"]];
            return $response->withJson($error);
        }

        // Try to GET memories on that user
        $q = "SELECT * FROM `user_memories` WHERE `user_id`=:user_id AND `memories_with`=:regno";
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':regno' => $args['id'],
            ':user_id' => $userId
        ]);
        $memories = $stmt->fetch(PDO::FETCH_OBJ);

        $result = ['user' => $user, 'memories' => $memories];
        return $response->withJson($result);

    } catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Put Memories record belonging to a user for a specific Relation
 */
$app->post('/memories/put/{id}',  function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];
    $directory = $this->get('settings')['memories_directory'];

    // Input validation
    if(empty($args['id'])) {
        $error = ['error' => ['text' => 'id cannot be empty']];
        return $response->withJson($error);
    }

    try {
        // Try to find user
        $sql = "SELECT `id`,`name` FROM `users` WHERE tec_regno=:id";
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':id' => $args['id']
        ]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if($user == false) {
            $error = ['error' => ['text' => "User does not exist"]];
            return $response->withJson($error);
        }

        // Try to GET memories on that user
        $q = "SELECT * FROM `user_memories` WHERE `user_id`=:user_id AND `memories_with`=:regno";
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':regno' => $args['id'],
            ':user_id' => $userId
        ]);

        $memories = $stmt->fetch(PDO::FETCH_OBJ);


        if($stmt->rowCount() == 0) {
            // Record not exists, INSERT
            $q = "INSERT INTO user_memories (`user_id`, `memories_with`, `text`, `img_path`) VALUES (:user_id, :memories_with, :text, :img_path)";
        } else {
            // Else, UPDATE
            $q = "UPDATE user_memories SET `text`=:text, `img_path`=:img_path WHERE `user_id`=:user_id AND `memories_with`=:memories_with";
        }

        // Try to save image
        $uploadedFiles = $request->getUploadedFiles();

        if(array_key_exists("img", $uploadedFiles)) {
            $uploadedFile = $uploadedFiles['img'];
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                $basename = bin2hex(random_bytes(8));
                $filename = uniqid($userId . "_" . $args['id'] . "_") . sprintf('%s.%0.8s', $basename, $extension);

                if ($memories != false) {
                    $existing_filenm = str_replace("memories://", $directory, $memories->img_path);
                    if (@file_exists($existing_filenm)) unlink($existing_filenm);
                }

                $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

                $file_dbentry = "memories://" . $filename;
            } else {
                $file_dbentry = $memories->img_path;
            }
        } else {
            $file_dbentry = $memories->img_path;
        }

        $stmt = $db->prepare($q);
        $stmt->execute([
            ':memories_with' => $args['id'],
            ':user_id' => $userId,
            ':text' => $request->getParam("text"),
            ':img_path' => $file_dbentry
        ]);

        $result = ['success' => true];
        return $response->withJson($result);

    } catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});