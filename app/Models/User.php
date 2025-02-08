<?php
// app/Models/User.php
namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/../../config/database.php';

class User
{
    public int $id;
    public string $email;
    public ?string $name;
    public ?string $google_id;

    public static function findByEmail(string $email): ?array
    {
        $pdo = getPDO();
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = getPDO();
        // Build the base SQL and parameters
        $sql = "INSERT INTO users (email, name, google_id, password, created_at) 
                VALUES (:email, :name, :google_id, :password, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':email'     => $data['email'],
            ':name'      => $data['name'] ?? null,
            ':google_id' => $data['google_id'] ?? null,
            ':password'  => $data['password'] ?? null
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function updateGoogleID(int $userId, string $googleId): bool
    {
        $pdo = getPDO();
        $sql = "UPDATE users 
                SET google_id = :google_id,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':google_id' => $googleId,
            ':id'        => $userId
        ]);
    }
}
