<?php
// config/database.php

function getPDO()
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=localhost;dbname=angels_db;charset=utf8mb4';
        $username = 'root';
        $password = 'root';
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $pdo = new PDO($dsn, $username, $password, $options);
    }
    return $pdo;
}
