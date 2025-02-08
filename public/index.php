<?php
// public/index.php

session_start();

// Global error handler: Convert errors into exceptions.
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
});

// Global exception handler: Sends error response with proper HTTP status.
set_exception_handler(function ($exception) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // If API request, return JSON error; otherwise, plain text.
    if (strpos($uri, '/api/') === 0) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode([
            'error' => $exception->getMessage(),
            'code'  => $exception->getCode()
        ]);
    } else {
        http_response_code(500);
        header('Content-Type: text/plain');
        echo "Internal Server Error: " . $exception->getMessage();
    }
    exit;
});

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Import controllers
use App\Controllers\InvoiceController;
use App\Controllers\AuthController;
use App\Controllers\AuthAPIController;
use App\Controllers\InvoiceAPIController;

// Parse request URI and method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Adjust API and Web routes to work with the new structure
$apiPrefix = '/api';
$oldPrefix = '/old';

if (strpos($uri, $apiPrefix) === 0) {
    // API Routes
    $authAPIController = new AuthAPIController();
    $invoiceApiController = new InvoiceAPIController();

    if ($uri === "$apiPrefix/auth/me" && $method === 'POST') {
        $authAPIController->me();
        exit;
    } elseif ($uri === "$apiPrefix/auth/signin" && $method === 'POST') {
        $authAPIController->emailSignIn();
        exit;
    } elseif ($uri === "$apiPrefix/auth/signup" && $method === 'POST') {
        $authAPIController->emailSignUp();
        exit;
    } elseif ($uri === "$apiPrefix/invoices" && $method === 'GET') {
        $invoiceApiController->index();
        exit;
    } elseif ($uri === "$apiPrefix/invoices" && $method === 'POST') {
        $invoiceApiController->store();
        exit;
    } elseif (preg_match("#^$apiPrefix/invoices/(\d+)$#", $uri, $matches)) {
        $invoiceId = (int)$matches[1];
        if ($method === 'GET') {
            $invoiceApiController->show($invoiceId);
            exit;
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            $invoiceApiController->update($invoiceId);
            exit;
        } elseif ($method === 'DELETE') {
            $invoiceApiController->destroy($invoiceId);
            exit;
        }
    } elseif (preg_match("#^$apiPrefix/invoices/(\d+)/pdf$#", $uri, $matches) && $method === 'GET') {
        $invoiceApiController->generatePDF((int)$matches[1]);
        exit;
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'API endpoint not found']);
        exit;
    }
} elseif (strpos($uri, $oldPrefix) === 0) {
    // Web Routes for old PHP views
    $invoiceController = new InvoiceController();
    $authController = new AuthController();

    if ($uri === "$oldPrefix/auth/google") {
        $authController->googleLogin();
    } elseif ($uri === "$oldPrefix/auth/google/callback") {
        $authController->googleCallback();
    } elseif ($uri === "$oldPrefix/logout") {
        $authController->logout();
    } elseif ($uri === "$oldPrefix/invoices" && $method === 'GET') {
        $invoiceController->index();
    } elseif ($uri === "$oldPrefix/invoices/create" && $method === 'GET') {
        $invoiceController->create();
    } elseif ($uri === "$oldPrefix/invoices/store" && $method === 'POST') {
        if (!empty($_POST['lines'])) {
            $_POST['lines'] = json_decode($_POST['lines'], true);
        }
        $invoiceController->store();
    } elseif (preg_match("#^$oldPrefix/invoices/(\d+)/edit$#", $uri, $matches)) {
        if ($method === 'GET') {
            $invoiceController->edit((int)$matches[1]);
        }
    } elseif (preg_match("#^$oldPrefix/invoices/(\d+)/update$#", $uri, $matches)) {
        if ($method === 'POST') {
            if (!empty($_POST['lines'])) {
                $_POST['lines'] = json_decode($_POST['lines'], true);
            }
            $invoiceController->update((int)$matches[1]);
        }
    } elseif (preg_match("#^$oldPrefix/invoices/(\d+)/delete$#", $uri, $matches)) {
        if ($method === 'GET') {
            $invoiceController->destroy((int)$matches[1]);
        }
    } elseif (preg_match("#^$oldPrefix/invoices/(\d+)/pdf$#", $uri, $matches)) {
        if ($method === 'GET') {
            $invoiceController->generatePDF((int)$matches[1]);
        }
    } else {
        http_response_code(404);
        header('Content-Type: text/plain');
        echo "Page not found.";
    }
} else {
    http_response_code(404);
    header('Content-Type: text/plain');
    echo "Page not found.";
}
