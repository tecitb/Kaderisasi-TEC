<?php

/**
 * TEC internApp Relations Backend
 * This route stores users' Relation and the respective vCard strings.
 * @author Muhammad Aditya Hilmy
 */

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Get Relations record belonging to a user
 */
$app->get('/relations/get',  function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $sql = "SELECT user_relations.id, relation_with, full_name, tec_regno FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id
            WHERE `user_id`=:user_id";

    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId
        ]);

        $relations = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        return $response->withJson($relations);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Get relation record details
 */
$app->get('/relations/details/{id}',  function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $sql = "SELECT user_relations.*, users.tec_regno FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id
            WHERE `user_id`=:user_id AND `user_relations`.`id`=:id";

    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':id' => $args['id']
        ]);

        $relations = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;
        return $response->withJson($relations);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Put new relation
 * @param relation_with String Related entity, acting as a primary key.
 */
$app->put('/relations/put/{relation_with}', function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    try {
        $sql = "SELECT * FROM `user_relations` WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':relation_with' => $args['relation_with']
        ]);

        $numRows = $stmt->rowCount();

        $vcard = $request->getParam("vcard");
        $fullName = $request->getParam("full_name");

        if($numRows > 0) {
            // If row exists, update existing one
            $qi = "UPDATE `user_relations` SET `vcard`=:vcard, `full_name`=:full_name WHERE `user_id`=:user_id AND `relation_with`=:relation_with";
        } else {
            // If row does not exist, create new
            $qi = "INSERT INTO `user_relations` (`user_id`, `relation_with`, `vcard`, `full_name`) VALUES (:user_id, :relation_with, :vcard, :full_name)";
        }

        $si = $db->prepare($qi);
        $si->execute([
            ':user_id' => $userId,
            ':relation_with' => $args['relation_with'],
            ':vcard' => $vcard,
            ':full_name' => $fullName
        ]);
        return $response->withJson(array("success" => true));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});


/**
 * Deletes relation
 */
$app->delete('/relations/delete/{id}', function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    try {
        $sql = "DELETE FROM `user_relations` WHERE `user_id`=:user_id AND `id`=:id";

        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':id' => $args['id']
        ]);
        return $response->withJson(array("success" => true));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Generates user vCard
 */
$app->get('/relations/vcard', function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    try {
        $sql = "SELECT * FROM users WHERE id=:user_id";

        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
        ]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        $vcard = "BEGIN:VCARD\n";
        if($user->name != null) $vcard .= "FN:".$user->name."\n";
        $vcard .= "ORG:Techno Entrepreneur Club ITB\n";
        if($user->mobile != null) $vcard .= "TEL;CELL:".$user->mobile."\n";
        if($user->address != null) $vcard .= "ADR;HOME:".$user->address."\n";
        if($user->tec_regno != null) $vcard .= "UID:".$user->tec_regno."\n";
        if($user->about_me != null) $vcard .= "NOTE:".$user->about_me."\n";
        if($user->line_id != null) $vcard .= "X-LINE:".$user->line_id."\n";
        if($user->instagram != null) $vcard .= "X-INSTAGRAM:".$user->instagram."\n";
        if($user->interests != null) $vcard .= "X-INTERESTS:".$user->interests."\n";
        $vcard .= "END:VCARD";

        return $response->withJson(array("vcard" => utf8_encode($vcard)));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Generates user vCard BY ADMIN
 */

$app->get('/relations/vcard/{user_id}', function(Request $request, Response $response, array $args) {
    $isAdmin = $request->getAttribute("jwt")['id'];

    if(!$isAdmin) {
        return $this->response->withJson(['error' => true, 'message' => 'Unauthorized']);
    }

    $userId = $args['user_id'];

    try {
        $sql = "SELECT * FROM users WHERE id=:user_id";

        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
        ]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        $vcard = "BEGIN:VCARD\n";
        if($user->name != null) $vcard .= "FN:".$user->name."\n";
        $vcard .= "ORG:Techno Entrepreneur Club ITB\n";
        if($user->mobile != null) $vcard .= "TEL;CELL:".$user->mobile."\n";
        if($user->address != null) $vcard .= "ADR;HOME:".$user->address."\n";
        if($user->tec_regno != null) $vcard .= "UID:".$user->tec_regno."\n";
        if($user->about_me != null) $vcard .= "NOTE:".$user->about_me."\n";
        if($user->line_id != null) $vcard .= "X-LINE:".$user->line_id."\n";
        if($user->instagram != null) $vcard .= "X-INSTAGRAM:".$user->instagram."\n";
        if($user->interests != null) $vcard .= "X-INTERESTS:".$user->interests."\n";
        $vcard .= "END:VCARD";

        return $response->withJson(array("vcard" => utf8_encode($vcard)));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});