<?php
// app/Controllers/AuthAPIController.php

namespace App\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;

class AuthAPIController
{
    public function me()
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['message' => 'Not authenticated']);
            return;
        }

        $user = User::findById($userId);
        echo json_encode(['user' => $user]);
    }

    public function emailSignIn()
    {
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        $email = $inputData['email'] ?? '';
        $password = $inputData['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $token = $this->generateJWT($user['id']);
            echo json_encode(['success' => true, 'token' => $token, 'user' => $user]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function emailSignUp()
    {
        $inputData = json_decode(file_get_contents("php://input"), true);
    
        $email = $inputData['email'] ?? '';
        $password = $inputData['password'] ?? '';
        $name = $inputData['name'] ?? '';

        if (empty($email) || empty($password) || empty($name)) {
            http_response_code(400);
            echo json_encode(['error' => 'All fields are required']);
            return;
        }

        if (User::findByEmail($email)) {
            http_response_code(409);
            echo json_encode(['error' => 'User already exists']);
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = User::create(['email' => $email, 'name' => $name, 'password' => $hash]);

        $token = $this->generateJWT($userId);
        echo json_encode(['success' => true, 'token' => $token]);
    }

    private function generateJWT($userId)
    {
        if (!isset($_ENV['JWT_SECRET']) || empty($_ENV['JWT_SECRET'])) {
            throw new Exception('JWT secret key is missing.');
        }
        
        $payload = [
            "user_id" => $userId,
            "iat" => time(),
            "exp" => time() + 3600 // 1-hour expiration
        ];
        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}
