<?php
// app/Controllers/InvoiceController.php
namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\PDFService;

class InvoiceController
{
    public function index()
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        $userId   = (int)$_SESSION[SESSION_USER]['id'];
        $invoices = Invoice::allForUser($userId);
        require __DIR__ . '/../Views/invoice/list.php';
    }

    public function create()
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        require __DIR__ . '/../Views/invoice/create.php';
    }

    public function store()
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        $userId = (int)$_SESSION[SESSION_USER]['id'];

        $invoiceData = [
            'user_id'        => $userId,
            'invoice_number' => $_POST['invoice_number'] ?? '',
            'invoice_date'   => $_POST['invoice_date'] ?? date('Y-m-d'),
            'to_name'        => $_POST['to_name'] ?? '',
            'city'           => $_POST['city'] ?? '',
            'invoice_type'   => (int)($_POST['invoice_type'] ?? 1)
        ];

        $invoiceId = Invoice::create($invoiceData);


        if (!empty($_POST['lines'])) {
            foreach ($_POST['lines'] as $line) {
                $lineData = [
                    'invoice_id'  => $invoiceId,
                    'description' => $line['description'],
                    'quantity'    => $line['quantity'],
                    'price'       => $line['price'],
                    'total'       => $line['quantity'] * $line['price']
                ];
                InvoiceLine::create($lineData);
            }
        }

        header('Location: /invoices');
        exit;
    }

    public function edit($id)
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        $userId  = (int)$_SESSION[SESSION_USER]['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            http_response_code(403);
            echo "Invoice not found or permission denied.";
            return;
        }
        $lines = InvoiceLine::findByInvoice($id);
        require __DIR__ . '/../Views/invoice/edit.php';
    }

    public function update($id)
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        $invoiceData = [
            'invoice_number' => $_POST['invoice_number'] ?? '',
            'invoice_date'   => $_POST['invoice_date'] ?? date('Y-m-d'),
            'to_name'        => $_POST['to_name'] ?? '',
            'city'           => $_POST['city'] ?? '',
            'invoice_type'   => (int)($_POST['invoice_type'] ?? 1)
        ];
        Invoice::update($id, $invoiceData);

        if (!empty($_POST['lines'])) {
            foreach ($_POST['lines'] as $line) {
                $lineId = (int)$line['id'];
                if ($lineId === 0) {
                    InvoiceLine::create([
                        'invoice_id'  => $id,
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'price'       => $line['price'],
                        'total'       => $line['quantity'] * $line['price']
                    ]);
                } else {
                    InvoiceLine::update($lineId, [
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'price'       => $line['price'],
                        'total'       => $line['quantity'] * $line['price']
                    ]);
                }
            }
        }

        header('Location: /invoices');
        exit;
    }

    public function destroy($id)
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        Invoice::delete($id);
        header('Location: /invoices');
        exit;
    }

    public function destroyLine($lineId)
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        InvoiceLine::delete($lineId);
        echo json_encode(['success' => true]);
    }

    public function generatePDF($id)
    {
        if (empty($_SESSION[SESSION_USER])) {
            header('Location: /auth/google');
            exit;
        }
        $userId  = (int)$_SESSION[SESSION_USER]['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found.";
            return;
        }
        $lines = InvoiceLine::findByInvoice($id);

        $path = PDFService::generateInvoicePDF($invoice, $lines);

        echo "PDF generated at: $path";
    }
}
