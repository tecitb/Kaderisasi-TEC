<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// PEER TO PEER SCORE
$app->post('/p2pscore/{id:[0-9]+}',function(Request $request, Response $response, array $args) {

    // Input validation
    if(empty($args['id'])) {
        $error = ['error' => ['text' => 'id cannot be empty']];
        return $response->withJson($error);
    }

    $sql = "INSERT INTO `peer_to_peer`(`penilai`,`dinilai`,`nilai`) VALUES (:penilai, :dinilai, :nilai)";

    $nilai = $request->getParam("nilai");

    if ($nilai < 1 || $nilai > 5) {
      $error = ['error' => ['text' => 'Nilai hanya bisa bernilai dari satu sampai lima']];
      return $response->withJson($error);
    }

    try {
        $db = $this->get('db');

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':penilai' => $request->getAttribute("jwt")['id'],
            ':dinilai' => $args['id'],
            ':nilai' => $nilai
        ]);
        $data = ["notice"=>["type"=>"success", "text" => "Nilai diterima"]];
        return $response->withJson($data);
    }
    catch (PDOException $e) {
        $error = ['error' => ['text' => $e->getMessage()]];
        return $response->withJson($error);
    }
});