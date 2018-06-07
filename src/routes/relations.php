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

    $sql = "SELECT id, relation_with, full_name FROM `user_relations` WHERE `user_id`=:user_id";

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

    $sql = "SELECT * FROM `user_relations` WHERE `user_id`=:user_id AND `id`=:id";

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