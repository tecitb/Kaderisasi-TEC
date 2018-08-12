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

    $sql = "SELECT user_relations.id, relation_with, full_name, tec_regno, vcard FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id
            WHERE `user_id`=:user_id AND `is_deleted`='0'";

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
$app->get('/relations/details/{relation_with}',  function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $sql = "SELECT user_relations.*, users.tec_regno FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id
            WHERE `user_id`=:user_id AND `user_relations`.`relation_with`=:relation_with AND `is_deleted`='0'";

    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':relation_with' => $args['relation_with']
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
            $relation = $stmt->fetch(PDO::FETCH_OBJ);
            $id = $relation['id'];
            // If row exists, update existing one
            $qi = "UPDATE `user_relations` SET `vcard`=:vcard, `full_name`=:full_name WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

            $si = $db->prepare($qi);
            $si->execute([
                ':user_id' => $userId,
                ':relation_with' => $args['relation_with'],
                ':vcard' => $vcard,
                ':full_name' => $fullName
            ]);
        } else {
            // If row does not exist, create new
            $qi = "INSERT INTO `user_relations` (`user_id`, `relation_with`, `vcard`, `full_name`) VALUES (:user_id, :relation_with, :vcard, :full_name)";

            $si = $db->prepare($qi);
            $si->execute([
                ':user_id' => $userId,
                ':relation_with' => $args['relation_with'],
                ':vcard' => $vcard,
                ':full_name' => $fullName
            ]);

            $id = $db->lastInsertId();
        }
        return $response->withJson(array("success" => true, "relationId" => $id));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});


/**
 * Deletes relation
 */
$app->delete('/relations/delete/{relation_with}', function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    try {
        $sql = "DELETE FROM `user_relations` WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':relation_with' => $args['relation_with']
        ]);
        return $response->withJson(array("success" => true));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * Syncs relations record.
 * JSON payload format:
 *
 * Array of
 *  Object of
 *      - timestamp
 *      - relation_uid
 *      - action [A, D]
 *      - vcard_payload
 *      - full_name
 */
$app->post('/relations/sync', function(Request $request, Response $response, array $args) {
    $userId = $request->getAttribute("jwt")['id'];

    $raw = $request->getParam("records");
    $records = json_decode($raw, true);

    try {

        // Iterate through the Request to Modify records
        foreach ($records as $record) {
            $rUid = $record['relation_uid'];
            $timestamp = $record['timestamp'];

            // Check if record exists
            $sql = "SELECT * FROM `user_relations` WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

            $db = $this->get('db');
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':relation_with' => $rUid
            ]);

            $numRows = $stmt->rowCount();
            $relation = $stmt->fetch(PDO::FETCH_ASSOC);

            switch ($record['action']) {
                // Request to append
                case 'A':
                    $vcard = $record['vcard_payload'];
                    $fullName = $record['full_name'];

                    // Record exists
                    if ($numRows > 0) {
                        // Check if request to append is newer than the last modified
                        if ($timestamp >= $relation['last_modified']) {
                            // OK to modify
                            // If row exists, update existing one
                            $qi = "UPDATE `user_relations` SET `vcard`=:vcard, `full_name`=:full_name, `last_modified`=:last_modified, `is_deleted`='0' WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

                            $si = $db->prepare($qi);
                            $si->execute([
                                ':user_id' => $userId,
                                ':relation_with' => $rUid,
                                ':vcard' => $vcard,
                                ':full_name' => $fullName,
                                ':last_modified' => $timestamp
                            ]);
                        }
                    } else {
                        // If row does not exist, create new
                        $qi = "INSERT INTO `user_relations` (`user_id`, `relation_with`, `vcard`, `full_name`, `last_modified`, `is_deleted`) VALUES (:user_id, :relation_with, :vcard, :full_name, :last_modified, '0')";

                        $si = $db->prepare($qi);
                        $si->execute([
                            ':user_id' => $userId,
                            ':relation_with' => $rUid,
                            ':vcard' => $vcard,
                            ':full_name' => $fullName,
                            ':last_modified' => $timestamp
                        ]);
                    }
                    break;
                case 'D':
                    // Record exists
                    if ($numRows > 0) {
                        // Check if request to append is newer than the last modified
                        if ($timestamp >= $relation['last_modified']) {
                            $sql = "UPDATE `user_relations` SET `is_deleted`='1', `last_modified`=:last_modified WHERE `user_id`=:user_id AND `relation_with`=:relation_with";

                            $db = $this->get('db');
                            $stmt = $db->prepare($sql);
                            $stmt->execute([
                                ':user_id' => $userId,
                                ':relation_with' => $rUid,
                                ':last_modified' => $timestamp
                            ]);
                        }
                    }
                    break;
            }
        }
        $error = ['success' => true];
        return $error;
    } catch (PDOException $e) {
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
        if($user->name != '') $vcard .= "FN:".$user->name."\n";
        $vcard .= "ORG:Techno Entrepreneur Club ITB\n";
        if($user->mobile != '') $vcard .= "TEL;CELL:".$user->mobile."\n";
        if($user->address != '') $vcard .= "ADR;HOME:".$user->address."\n";
        if($user->tec_regno != '') $vcard .= "UID:".$user->tec_regno."\n";
        if($user->about_me != '') $vcard .= "NOTE:".$user->about_me."\n";
        if($user->line_id != '') $vcard .= "X-LINE:".$user->line_id."\n";
        if($user->instagram != '') $vcard .= "X-INSTAGRAM:".$user->instagram."\n";
        if($user->interests != '') $vcard .= "X-INTERESTS:".$user->interests."\n";
        $vcard .= "PHOTO:".BASE_URL."/api/dp/".$user->id."\n";
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
        if($user->name != '') $vcard .= "FN:".$user->name."\n";
        $vcard .= "ORG:Techno Entrepreneur Club ITB\n";
        if($user->mobile != '') $vcard .= "TEL;CELL:".$user->mobile."\n";
        if($user->address != '') $vcard .= "ADR;HOME:".$user->address."\n";
        if($user->tec_regno != '') $vcard .= "UID:".$user->tec_regno."\n";
        if($user->about_me != '') $vcard .= "NOTE:".$user->about_me."\n";
        if($user->line_id != '') $vcard .= "X-LINE:".$user->line_id."\n";
        if($user->instagram != '') $vcard .= "X-INSTAGRAM:".$user->instagram."\n";
        if($user->interests != '') $vcard .= "X-INTERESTS:".$user->interests."\n";
        $vcard .= "PHOTO:".BASE_URL."/api/dp/".$user->id."\n";
        $vcard .= "END:VCARD";

        return $response->withJson(array("vcard" => utf8_encode($vcard)));
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/**
 * [Administrative endpoint]
 * List relations network (nodes and edges)
 */
$app->get('/relations/network/{tecregno}',  function(Request $request, Response $response, array $args) {
    if ($request->getAttribute("jwt")['isAdmin'] != 1) {
        $error = ['error' => ['text' => 'Permission denied']];
        return $response->withJson($error);
    }

    $groupBy = $request->getParam('grouping');
    if(!empty($groupBy)) {
        $groupBy = "_group__".$request->getParam('grouping');
        if (!function_exists($groupBy)) {
            $error = ['error' => ['text' => 'Invalid grouping method']];
            return $response->withJson($error);
        }
    }

    $tecRegNo = $args['tecregno'];
    if($tecRegNo === "all") {
        // Filter is undefined, print all relations
        $sql = "SELECT user_relations.id, relation_with, full_name, tec_regno, vcard, users.name AS entity_name FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id";
    } else {
        $sql = "SELECT user_relations.id, relation_with, full_name, tec_regno, vcard, users.name AS entity_name FROM `user_relations` 
            LEFT JOIN users ON user_relations.user_id = users.id
            WHERE `tec_regno`=:tec_regno OR `relation_with`=:tec_regno";
    }

    try {
        $db = $this->get('db');
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':tec_regno' => $tecRegNo
        ]);

        $relations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $ntrac = array();
        $nodes = array();
        $edges = array();
        $nodes_info = array();

        foreach ($relations as $row) {
            $tno = $row['tec_regno'];
            $rw = $row['relation_with'];

            if(empty($ntrac[$tno])) {
                $ntrac[$tno] = true;
                if(!empty($groupBy)) $group = $groupBy($db, $tno);
                else $group = "default";

                $node = array("id" => $tno, "label" => $tno, "group" => $group);
                array_push($nodes, $node);

                $node_info = array("entity_id" => $tno, "dn" => $row['entity_name']);
                array_push($nodes_info, $node_info);
            }

            if(empty($ntrac[$rw])) {
                $ntrac[$rw] = true;
                if(!empty($groupBy)) $group = $groupBy($db, $rw);
                else $group = "default";

                $node = array("id" => $rw, "label" => $rw, "group" => $group);
                array_push($nodes, $node);

                $node_info = array("entity_id" => $rw, "dn" => $row['full_name']);
                array_push($nodes_info, $node_info);
            }

            $edge = array("from" => $tno, "to" => $rw, "arrows" => "to");
            array_push($edges, $edge);
        }

        $aresp = ["nodes" => $nodes, "edges" => $edges, "node_info" => $nodes_info];
        return $response->withJson($aresp);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});

/* Relations grouping filters */
/**
 * Groups entities by NIM
 * @param $entityId Entity ID
 * @return bool|string
 */
function _group__nim(&$db, $entityId) {
    $q = "SELECT `NIM` FROM `users` WHERE `tec_regno`=:tec_regno";
    $stmt = $db->prepare($q);
    $stmt->execute([
        ':tec_regno' => $entityId
    ]);

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (strlen(@$user['NIM']) == 8)
            return substr($user['NIM'], 0, 5);
        else
            return "user";
    } else {
        return "default";
    }
}

/**
 * Groups entities by entityId
 * @param $entityId
 * @return string
 */
function _group__entityId(&$db, $entityId) {
    if(substr($entityId, 0, 3) === "TEC") return "peserta";
    else return "";
}

/**
 * Groups entities by isActive status
 * @param $entityId
 * @return string
 */
function _group__in_training(&$db, $entityId) {
    if(_group__entityId($db, $entityId) === "peserta") {
        $q = "SELECT `is_active` FROM `users` WHERE `tec_regno`=:tec_regno";
        $stmt = $db->prepare($q);
        $stmt->execute([
            ':tec_regno' => $entityId
        ]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user['is_active'] == 1)
                return "active";
            else
                return "inactive";
        } else {
            return "default";
        }
    } else {
        return "default";
    }
}