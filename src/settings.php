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
            'name' => 'goaltracker',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        // DB settings
        'db' => [
            'host' => 'localhost',
            'db_name' => 'realjv3_goaltracker',
            'id' => 'realjv3_realjv3',
            'password' => 'Passw0rd'
        ],
    ],
];
