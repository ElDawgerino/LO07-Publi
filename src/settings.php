<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        'database' => [
            'dsn' => 'mysql:dbname=lo07_projet;host=localhost',
            'username' => 'app_lo07',
            'password' => 'app_lo07_mdp'
        ]
    ],
];
