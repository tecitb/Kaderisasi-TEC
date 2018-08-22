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
            "user" => getenv('DB_USERNAME'),
            "pass" => getenv('DB_PASSWORD')
        ],
        "jwt" => [
            'secret' => getenv('JWT_SECRET')
        ],
        "spaces" => [
            "key" => getenv("DO_SPACES_KEY"),
            "secret" => getenv("DO_SPACES_SECRET"),
            "region" => getenv("DO_SPACES_REGION"),
            "name" => getenv("DO_SPACES_BUCKET_NAME"),
        ],
        "smtp" => [
            "host" => getenv("SMTP_HOST") ?: "localhost",
            "port" => getenv("SMTP_PORT") ?: 587,
            "security" => getenv("SMTP_SECURITY") ?: "tls",
            "username" => getenv("SMTP_AUTH_USERNAME") ?: "admin@tec.or.id",
            "password" => getenv("SMTP_AUTH_PASSWORD") ?: "",
            "from_address" => getenv("SMTP_FROM_ADDRESS") ?: "admin@tec.or.id",
            "from_name" => getenv("SMTP_FROM_NAME") ?: "TEC Internship 2018",
            "reply_to" => getenv("SMTP_REPLY_TO") ?: "internship@tec.or.id",
        ],
        'profile_directory' =>  dirname(__DIR__) . '/uploads/profile',
        'assignment_directory' =>  dirname(__DIR__) . '/uploads/assignment',
        'memories_directory' =>  dirname(__DIR__) . '/uploads/memories'
    ],
];
