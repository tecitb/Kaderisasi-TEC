<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        "db" => [
            "host" => getenv('DB_HOST'),
            "dbname" => getenv('DB_NAME'),
            "user" => getenv('DB_USER'),
            "pass" => getenv('DB_PASSWORD')
        ],
        "jwt" => [
            'secret' => getenv('JWT_SECRET')
        ]
    ],
];
