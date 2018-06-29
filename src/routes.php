<?php

use Slim\Http\Request;
use Slim\Http\Response;
use \Firebase\JWT\JWT;

// Routes

$app->get('/[{name}]', function (Request $request, Response $response, array $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view

    return $this->renderer->render($response, 'index.phtml', $args);
});


$app->group('/api', function(\Slim\App $app) {
    // $app->get('/jwt',function(Request $request, Response $response, array $args) {
    //   return $this->response->withJson($request->getAttribute("jwt"));
    // });

    require_once __DIR__ . '/routes/public.php';
    require_once __DIR__ . '/routes/user.php';
    require_once __DIR__ . '/routes/quiz.php'; 
    require_once __DIR__ . '/routes/assignment.php';
    require_once __DIR__ . '/routes/relations.php';
    require_once __DIR__ . '/routes/admin_only.php';
});