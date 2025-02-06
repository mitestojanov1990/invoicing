<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use Google\Client as GoogleClient;
use Google\Service\Oauth2 as GoogleOauth2;
use App\Models\User;

class AuthController
{
    protected GoogleClient $client;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/google.php';

        $this->client = new GoogleClient();
        $this->client->setClientId($config['client_id']);
        $this->client->setClientSecret($config['client_secret']);
        $this->client->setRedirectUri($config['redirect_uri']);
        $this->client->setScopes($config['scopes']);
    }

    public function googleLogin()
    {
        // Optional state param for security
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth2state'] = $state;
        $this->client->setState($state);

        $authUrl = $this->client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback()
    {
        // Check state param
        if (
            isset($_GET['state']) && 
            $_GET['state'] !== $_SESSION['oauth2state']
        ) {
            unset($_SESSION['oauth2state']);
            exit('Invalid state');
        }

        // Exchange auth code for token
        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                exit('Error fetching access token: ' . htmlspecialchars($token['error']));
            }

            // Store token if needed
            $_SESSION['google_access_token'] = $token['access_token'];

            // Fetch user profile from Google
            $oauth2 = new GoogleOauth2($this->client);
            $this->client->setAccessToken($token);
            $googleUser = $oauth2->userinfo->get();

            // e.g. $googleUser->email, $googleUser->id, $googleUser->name

            // 1) Check if a local user with this email exists
            $existingUser = User::findByEmail($googleUser->email);

            if ($existingUser) {
                // 2) If user exists, optionally update google_id if needed
                if (empty($existingUser['google_id'])) {
                    User::updateGoogleID($existingUser['id'], $googleUser->id);
                }
                $localUserId = $existingUser['id'];
            } else {
                // 3) Create new local user
                $localUserId = User::create([
                    'email'     => $googleUser->email,
                    'name'      => $googleUser->name,
                    'google_id' => $googleUser->id
                ]);
            }

            // 4) Store the local user ID in session
            $_SESSION['user'] = [
                'id'    => $localUserId,
                'email' => $googleUser->email,
                'name'  => $googleUser->name
            ];

            // Redirect to dashboard or invoice list
            header('Location: /invoices');
            exit;
        }

        // If no code param, something’s off
        exit('No code parameter returned from Google OAuth.');
    }

    public function logout()
    {
        unset($_SESSION['user'], $_SESSION['google_access_token']);
        header('Location: /');
        exit;
    }
}
