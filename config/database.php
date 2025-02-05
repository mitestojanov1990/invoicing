<?php
// config/database.php

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host='.($_ENV['DB_HOST'] ?? 'localhost').';dbname='.($_ENV['DB_NAME'] ?? 'angels_db').';charset=utf8mb4';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? 'root';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $pdo = new PDO($dsn, $username, $password, $options);
    }
    return $pdo;
}
