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
            'server' => 'localhost',
            'name' => 'lo07_projet',
            'username' => 'app_lo07',
            'password' => 'app_lo07_mdp'
        ]
    ],
];
