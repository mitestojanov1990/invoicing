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
        $this->client->setAccessType($config['access_type']);
        $this->client->setPrompt($config['prompt']);
        $this->client->setScopes($config['scopes']);
    }

    public function googleLogin()
    {
        $state = bin2hex(random_bytes(16));
        $_SESSION[OAUTH2STATE] = $state;
        $this->client->setState($state);

        $authUrl = $this->client->createAuthUrl();
        header('Location: ' . $authUrl);
        exit;
    }

    public function googleCallback()
    {
        if (
            isset($_GET['state']) && 
            $_GET['state'] !== $_SESSION[OAUTH2STATE]
        ) {            
            unset($_SESSION[OAUTH2STATE]);
            exit('Invalid state');
        }

        if (isset($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                exit('Error fetching access token: ' . htmlspecialchars($token['error']));
            }

            $_SESSION[GOOGLE_ACCESS_TOKEN] = $token['access_token'];

            $oauth2 = new GoogleOauth2($this->client);
            $this->client->setAccessToken($token);
            $googleUser = $oauth2->userinfo->get();

            $existingUser = User::findByEmail($googleUser->email);

            if ($existingUser) {
                if (empty($existingUser['google_id'])) {
                    User::updateGoogleID($existingUser['id'], $googleUser->id);
                }
                $localUserId = $existingUser['id'];
            } else {
                $localUserId = User::create([
                    'email'     => $googleUser->email,
                    'name'      => $googleUser->name,
                    'google_id' => $googleUser->id
                ]);
            }

            $_SESSION[SESSION_USER] = [
                'id'    => $localUserId,
                'email' => $googleUser->email,
                'name'  => $googleUser->name
            ];

            header('Location: /invoices');
            exit;
        }

        exit('No code parameter returned from Google OAuth.');
    }

    public function logout()
    {
        unset($_SESSION[SESSION_USER], $_SESSION[GOOGLE_ACCESS_TOKEN]);
        header('Location: /');
        exit;
    }
}
