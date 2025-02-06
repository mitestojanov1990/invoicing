<?php

require_once __DIR__ . '/vendor/autoload.php';

$dbHost = $_ENV['DB_HOST'] ?? 'localhost';
$dbName = $_ENV['DB_NAME'] ?? 'angels_db';
$dbUser = $_ENV['DB_USER'] ?? 'invoice_user';
$dbPass = $_ENV['DB_PASS'] ?? 'invpass';
$dbPort = $_ENV['DB_PORT'] ?? 8889;

return [
    'paths' => [
        'migrations' => __DIR__ . '/database/migrations',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',

        'development' => [
            'adapter' => 'mysql',
            'host' => $dbHost,
            'name' => $dbName,
            'user' => $dbUser,
            'pass' => $dbPass,
            'port' => $dbPort,
            'charset' => 'utf8mb4',
        ],

        'production' => [
            'adapter' => 'mysql',
            'host' => $dbHost,
            'name' => $dbName,
            'user' => $dbUser,
            'pass' => $dbPass,
            'port' => $dbPort,
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation'
];
