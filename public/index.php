<?php
// public/index.php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\InvoiceController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
ini_set('display_errors', 1);
error_reporting(E_ALL);
$invoiceController = new InvoiceController();

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
if ($uri === '/invoices') {
    if ($method === 'GET') {
        $invoiceController->index();
    }
} elseif ($uri === '/invoices/create') {
    if ($method === 'GET') {
        $invoiceController->create();
    }
} elseif ($uri === '/invoices/store') {
    if ($method === 'POST') {
        // lines come as JSON in $_POST['lines'], decode before sending to store
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
        // lines come as JSON, decode
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
