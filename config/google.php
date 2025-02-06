<?php
// config/google.php

return [
    'client_id'     => 'm.apps.googleusercontent.com',
    'client_secret' => 'LI',
    'redirect_uri'  => 'http://localhost:8888/auth/google/callback',
    'scopes'        => [
        'email',
        'profile',
    ],
];
