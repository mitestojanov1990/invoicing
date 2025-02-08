<?php
// public/index.php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/constants.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Controllers\InvoiceController;
use App\Controllers\AuthController;
use App\Controllers\AuthAPIController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$invoiceController = new InvoiceController();
$authController = new AuthController();

/**
 * Basic routes:
 * GET  /invoices                => $invoiceController->index()
 * GET  /invoices/create         => $invoiceController->create()
 * POST /invoices/store          => $invoiceController->store()
 * GET  /invoices/{id}/edit      => $invoiceController->edit($id)
 * POST /invoices/{id}/update    => $invoiceController->update($id)
 * GET  /invoices/{id}/delete    => $invoiceController->destroy($id)
 * GET  /invoices/{id}/pdf       => $invoiceController->generatePDF($id)
 */


// Basic router
if ($uri === '/api/auth/signin' && $method === 'POST') {
    $authAPIController = new AuthAPIController();
    $authAPIController->emailSignIn();
    exit;
} elseif ($uri === '/api/auth/signup' && $method === 'POST') {
    $authAPIController = new AuthAPIController();
    $authAPIController->emailSignUp();
    exit;
}


if ($uri === '/auth/google') {
    // GET /auth/google => redirect to google
    $authController->googleLogin();
} elseif ($uri === '/auth/google/callback') {
    // GET /auth/google/callback => handle google's redirect
    $authController->googleCallback();
} elseif ($uri === '/logout') {
    $authController->logout();
} else {
    if (!isset($_SESSION[SESSION_USER])) {
        // If not logged in, redirect to Google authentication
        header('Location: /auth/google');
        exit;
    }
}

if ($uri === '/invoices') {
    if (!isset($_SESSION[SESSION_USER])) {
        header('Location: /auth/google');
        exit;
    }
    if ($method === 'GET') {
        $invoiceController->index();
    }
} elseif ($uri === '/invoices/create') {
    if ($method === 'GET') {
        $invoiceController->create();
    }
} elseif ($uri === '/invoices/store') {
    if ($method === 'POST') {
        if (!empty($_POST['lines'])) {
            $_POST['lines'] = json_decode($_POST['lines'], true);
        }
        $invoiceController->store();
    }
} elseif (preg_match('#^/invoices/(\d+)/edit$#', $uri, $matches)) {
    if ($method === 'GET') {
        $invoiceController->edit((int)$matches[1]);
    }
} elseif (preg_match('#^/invoices/(\d+)/update$#', $uri, $matches)) {
    if ($method === 'POST') {
        if (!empty($_POST['lines'])) {
            $_POST['lines'] = json_decode($_POST['lines'], true);
        }
        $invoiceController->update((int)$matches[1]);
    }
} elseif (preg_match('#^/invoices/(\d+)/delete$#', $uri, $matches)) {
    if ($method === 'GET') {
        $invoiceController->destroy((int)$matches[1]);
    }
} elseif (preg_match('#^/invoices/(\d+)/pdf$#', $uri, $matches)) {
    if ($method === 'GET') {
        $invoiceController->generatePDF((int)$matches[1]);
    }
} else {
    http_response_code(404);
    echo "Page not found.";
}
