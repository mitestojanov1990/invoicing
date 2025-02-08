<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';

use App\Controllers\AuthAPIController;
use App\Controllers\InvoiceAPIController;
use App\Middleware\AuthMiddleware;

header("Access-Control-Allow-Origin: https://invoicing.dimitrycode.com");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

set_exception_handler(function ($exception) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => $exception->getMessage(), 'code' => $exception->getCode()]);
    exit;
});

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$apiPrefix = '/api';
$authAPIController = new AuthAPIController();
$invoiceApiController = new InvoiceAPIController();

if ($uri === "$apiPrefix/auth/me" && $method === 'GET') {
    AuthMiddleware::checkAuth();
    $authAPIController->me();
} elseif ($uri === "$apiPrefix/auth/signin" && $method === 'POST') {
    $authAPIController->emailSignIn();
} elseif ($uri === "$apiPrefix/auth/signup" && $method === 'POST') {
    $authAPIController->emailSignUp();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found']);
}
