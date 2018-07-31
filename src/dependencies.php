<?php
// DIC configuration
use Aws\S3\S3Client;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function($c) {
	$settings = $c->get('settings')['db'];
	$pdo = new PDO("mysql:host=".$settings['host'] . ";dbname=".$settings['dbname'], $settings['user'], $settings['pass']);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	return $pdo;
};

$container['spaces'] = function($c) {
    $settings = $c->get('settings')['spaces'];

    // Configure a client using Spaces
    $client = new Aws\S3\S3Client([
        'version' => 'latest',
        'region'  => $settings['region'],
        'endpoint' => 'https://' . $settings['region'] . '.digitaloceanspaces.com',
        'credentials' => [
            'key'    => $settings['key'],
            'secret' => $settings['secret'],
        ],
    ]);

    return $client;
};