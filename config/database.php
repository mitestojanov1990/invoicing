<?php
// config/database.php

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_NAME'].';charset=utf8mb4';
        $username = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASS'];

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $pdo = new PDO($dsn, $username, $password, $options);
    }
    return $pdo;
}
