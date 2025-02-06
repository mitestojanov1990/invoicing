<?php
// app/Controllers/InvoiceController.php
namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\PDFService;

class InvoiceController
{
    // Show all invoices
    public function index()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        $userId   = (int)$_SESSION['user']['id'];
        $invoices = Invoice::allForUser($userId);
        require __DIR__ . '/../Views/invoice/list.php';
    }

    // Show create form
    public function create()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        require __DIR__ . '/../Views/invoice/create.php';
    }

    // Store new invoice (and its lines)
    public function store()
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        $userId = (int)$_SESSION['user']['id'];

        $invoiceData = [
            'user_id'        => $userId,
            'invoice_number' => $_POST['invoice_number'] ?? '',
            'invoice_date'   => $_POST['invoice_date'] ?? date('Y-m-d'),
            'to_name'        => $_POST['to_name'] ?? '',
            'city'           => $_POST['city'] ?? '',
            'invoice_type'   => (int)($_POST['invoice_type'] ?? 1)
        ];

        $invoiceId = Invoice::create($invoiceData);


        // 2) Create each invoice line
        // Expecting line data from e.g. $_POST['lines'] as JSON or repeated fields
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

        // Redirect back to invoice list
        header('Location: /invoices');
        exit;
    }

    // Show edit form for an invoice
    public function edit($id)
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        $userId  = (int)$_SESSION['user']['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            http_response_code(403);
            echo "Invoice not found or permission denied.";
            return;
        }
        $lines = InvoiceLine::findByInvoice($id);
        require __DIR__ . '/../Views/invoice/edit.php';
    }

    // Update existing invoice
    public function update($id)
    {
        if (empty($_SESSION['user'])) {
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

        // Invoice lines
        // We have either updated lines or new lines. 
        // For simplicity, assume we get line id, desc, qty, price.
        // If id == 0 => create new line, else update existing line
        if (!empty($_POST['lines'])) {
            foreach ($_POST['lines'] as $line) {
                $lineId = (int)$line['id'];
                if ($lineId === 0) {
                    // New line
                    InvoiceLine::create([
                        'invoice_id'  => $id,
                        'description' => $line['description'],
                        'quantity'    => $line['quantity'],
                        'price'       => $line['price'],
                        'total'       => $line['quantity'] * $line['price']
                    ]);
                } else {
                    // Update existing line
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

    // Delete entire invoice (and lines)
    public function destroy($id)
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        Invoice::delete($id);
        header('Location: /invoices');
        exit;
    }

    // Delete a single invoice line (via AJAX or a form)
    public function destroyLine($lineId)
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        InvoiceLine::delete($lineId);
        // Usually return JSON or redirect
        echo json_encode(['success' => true]);
    }

    // Generate PDF for a given invoice
    public function generatePDF($id)
    {
        if (empty($_SESSION['user'])) {
            header('Location: /auth/google');
            exit;
        }
        $userId  = (int)$_SESSION['user']['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found.";
            return;
        }
        $lines = InvoiceLine::findByInvoice($id);

        $path = PDFService::generateInvoicePDF($invoice, $lines);

        // Return file path or force download, etc.
        echo "PDF generated at: $path";
    }
}
