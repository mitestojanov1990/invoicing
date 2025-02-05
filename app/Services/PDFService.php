<?php
// app/Services/PDFService.php
namespace App\Services;

use Mpdf\Mpdf;
use App\Models\Invoice;

class PDFService
{
    public static function generateInvoicePDF(array $invoice, array $lines): string
    {
        // $invoice is the invoice record from DB
        // $lines is an array of invoice line records

        // Figure out heading from invoice_type
        $t_type = (int)$invoice['invoice_type'];
        switch ($t_type) {
            case 1: $title = "Фактура бр."; break;
            case 2: $title = "Профактура бр."; break;
            case 3: $title = "Понуда бр."; break;
            default: $title = "Фактура бр.";
        }

        // Build lines HTML
        $lineRows = '';
        $grandTotal = 0;
        foreach ($lines as $l) {
            $lineRows .= "<tr>
                <td>{$l['description']}</td>
                <td style='text-align:right;'>{$l['quantity']}</td>
                <td style='text-align:right;'>{$l['price']}</td>
                <td style='text-align:right;'>{$l['total']}</td>
            </tr>";
            $grandTotal += $l['total'];
        }

        $html = "<html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: DejaVu Sans, sans-serif; }
                table { border-collapse: collapse; width:100%; }
                td, th { border:1px solid #000; padding:5px; }
                .header { text-align:center; }
            </style>
        </head>
        <body>
            <h2 class='header'>{$title} {$invoice['invoice_number']}</h2>
            <p>Датум: {$invoice['invoice_date']}</p>
            <p>До: {$invoice['to_name']}, {$invoice['city']}</p>

            <table>
              <thead>
                <tr>
                  <th>Опис</th>
                  <th>Количина</th>
                  <th>Цена</th>
                  <th>Вкупно</th>
                </tr>
              </thead>
              <tbody>
                {$lineRows}
              </tbody>
            </table>
            <h3 style='text-align:right;'>Вкупно: {$grandTotal}</h3>
        </body>
        </html>";

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        $fileName = uniqid() . '.pdf';
        $dir = self::getDirByType($t_type);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fullPath = "$dir/$fileName";
        $mpdf->Output($fullPath, 'F');

        // You might save the PDF doc name in your archive tables:
        // e.g. InvoiceArchive::save(...)
        // or do it here as needed.

        return $fullPath;
    }

    private static function getDirByType(int $type): string
    {
        // Adjust as needed for your structure
        $base = __DIR__ . '/../../public/documents';
        switch ($type) {
            case 1: return $base . '/fakturi';
            case 2: return $base . '/profakturi';
            case 3: return $base . '/ponudi';
            default: return $base . '/fakturi';
        }
    }
}
