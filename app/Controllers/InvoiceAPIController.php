<?php
// app/Controllers/Api/InvoiceAPIController.php
namespace App\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Services\PDFService;

class InvoiceAPIController
{
    /**
     * Helper: Check that the user is authenticated.
     */
    protected function requireAuth()
    {
        if (empty($_SESSION[SESSION_USER])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    /**
     * Helper: Send JSON response with the given data and HTTP status.
     */
    protected function sendResponse($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * GET /api/invoices
     * Returns all invoices for the authenticated user.
     */
    public function index()
    {
        $this->requireAuth();
        $userId = (int)$_SESSION[SESSION_USER]['id'];
        $invoices = Invoice::allForUser($userId);
        $this->sendResponse($invoices);
    }

    /**
     * GET /api/invoices/{id}
     * Returns details of a single invoice along with its lines.
     */
    public function show($id)
    {
        $this->requireAuth();
        $userId = (int)$_SESSION[SESSION_USER]['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            $this->sendResponse(['error' => 'Invoice not found'], 404);
        }
        $lines = InvoiceLine::findByInvoice($id);
        $invoice['lines'] = $lines;
        $this->sendResponse($invoice);
    }

    /**
     * POST /api/invoices
     * Creates a new invoice. Expects a JSON payload.
     */
    public function store()
    {
        $this->requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(['error' => 'Invalid JSON input'], 400);
        }

        $userId = (int)$_SESSION[SESSION_USER]['id'];
        $invoiceData = [
            'user_id'        => $userId,
            'invoice_number' => $input['invoice_number'] ?? '',
            'invoice_date'   => $input['invoice_date'] ?? date('Y-m-d'),
            'to_name'        => $input['to_name'] ?? '',
            'city'           => $input['city'] ?? '',
            'invoice_type'   => (int)($input['invoice_type'] ?? 1)
        ];

        $invoiceId = Invoice::create($invoiceData);
        if (!$invoiceId) {
            $this->sendResponse(['error' => 'Failed to create invoice'], 500);
        }

        if (!empty($input['lines'])) {
            foreach ($input['lines'] as $line) {
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

        $this->sendResponse(['message' => 'Invoice created', 'invoice_id' => $invoiceId], 201);
    }

    /**
     * PUT/PATCH /api/invoices/{id}
     * Updates an existing invoice. Expects a JSON payload.
     */
    public function update($id)
    {
        $this->requireAuth();

        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->sendResponse(['error' => 'Invalid JSON input'], 400);
        }

        $invoiceData = [
            'invoice_number' => $input['invoice_number'] ?? '',
            'invoice_date'   => $input['invoice_date'] ?? date('Y-m-d'),
            'to_name'        => $input['to_name'] ?? '',
            'city'           => $input['city'] ?? '',
            'invoice_type'   => (int)($input['invoice_type'] ?? 1)
        ];

        $updated = Invoice::update($id, $invoiceData);
        if (!$updated) {
            $this->sendResponse(['error' => 'Failed to update invoice'], 500);
        }

        if (isset($input['lines'])) {
            foreach ($input['lines'] as $line) {
                $lineId = isset($line['id']) ? (int)$line['id'] : 0;
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

        $this->sendResponse(['message' => 'Invoice updated']);
    }

    /**
     * DELETE /api/invoices/{id}
     * Deletes an invoice.
     */
    public function destroy($id)
    {
        $this->requireAuth();

        $deleted = Invoice::delete($id);
        if (!$deleted) {
            $this->sendResponse(['error' => 'Failed to delete invoice'], 500);
        }
        $this->sendResponse(['message' => 'Invoice deleted']);
    }

    /**
     * GET /api/invoices/{id}/pdf
     * Generates a PDF for an invoice.
     */
    public function generatePDF($id)
    {
        $this->requireAuth();

        $userId = (int)$_SESSION[SESSION_USER]['id'];
        $invoice = Invoice::findForUser($id, $userId);
        if (!$invoice) {
            $this->sendResponse(['error' => 'Invoice not found'], 404);
        }
        $lines = InvoiceLine::findByInvoice($id);
        $path = PDFService::generateInvoicePDF($invoice, $lines);
        if (!$path) {
            $this->sendResponse(['error' => 'Failed to generate PDF'], 500);
        }
        $this->sendResponse(['message' => 'PDF generated', 'pdf_path' => $path]);
    }
}
