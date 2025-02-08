<?php
// app/Controllers/AuthAPIController.php
namespace App\Controllers;

use App\Models\User;

class AuthAPIController
{
    public function me()
    {
        if (isset($_SESSION[SESSION_USER])) {
            echo json_encode(['user' => $_SESSION[SESSION_USER]]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Not authenticated']);
        }
    }
    
    public function emailSignIn()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = User::findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION[SESSION_USER] = [
                'id'    => $user['id'],
                'email' => $user['email'],
                'name'  => $user['name']
            ];
            echo json_encode(['success' => true, 'user' => $_SESSION[SESSION_USER]]);
        } else {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
        }
    }

    public function emailSignUp()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['name'] ?? '';

        $existingUser = User::findByEmail($email);
        if ($existingUser) {
            http_response_code(409);
            echo json_encode(['success' => false, 'message' => 'User already exists']);
            return;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $userId = User::create([
            'email'    => $email,
            'name'     => $name,
            'password' => $hash
        ]);
        $_SESSION[SESSION_USER] = [
            'id'    => $userId,
            'email' => $email,
            'name'  => $name
        ];
        echo json_encode(['success' => true, 'user' => $_SESSION[SESSION_USER]]);
    }
}
