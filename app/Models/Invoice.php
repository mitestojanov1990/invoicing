<?php
// app/Models/Invoice.php
namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/../../config/database.php';
// id (INT AI),
// invoice_number (VARCHAR),
// invoice_date (DATE),
// to_name (VARCHAR),
// city (VARCHAR),
// invoice_type (TINYINT),
// created_at (DATETIME),
// updated_at (DATETIME)
class Invoice
{
    public int $id;
    public string $invoice_number;
    public string $invoice_date; // e.g. 'YYYY-mm-dd'
    public string $to_name;
    public string $city;
    public int $invoice_type;    // 1=faktura, 2=profaktura, 3=ponuda

    // For demonstration, not showing all possible columns. 
    // In real usage, add all needed columns/properties.
    public static function create(array $data): int
    {
        $pdo = getPDO();
        $sql = "INSERT INTO invoices 
                (user_id, invoice_number, invoice_date, to_name, city, invoice_type, created_at) 
                VALUES (:user_id, :num, :dt, :to, :city, :type, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'], // local user ID from the session
            ':num'     => $data['invoice_number'],
            ':dt'      => $data['invoice_date'],
            ':to'      => $data['to_name'],
            ':city'    => $data['city'],
            ':type'    => $data['invoice_type']
        ]);
        return (int)$pdo->lastInsertId();
    }
    
    public static function allForUser(int $userId): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE user_id = :uid ORDER BY id DESC");
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll();
    }
    
    public static function findForUser(int $invoiceId, int $userId): ?array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = :id AND user_id = :uid LIMIT 1");
        $stmt->execute([':id' => $invoiceId, ':uid' => $userId]);
        $invoice = $stmt->fetch();
        return $invoice ?: null;
    }
    
    public static function update(int $id, array $data): bool
    {
        $pdo = getPDO();
        $sql = "UPDATE invoices
                SET invoice_number = :num,
                    invoice_date = :dt,
                    to_name = :to,
                    city = :city,
                    invoice_type = :type,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':num'  => $data['invoice_number'],
            ':dt'   => $data['invoice_date'],
            ':to'   => $data['to_name'],
            ':city' => $data['city'],
            ':type' => $data['invoice_type'],
            ':id'   => $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = getPDO();
        // Also delete invoice lines associated with this invoice
        $pdo->beginTransaction();
        try {
            $lineStmt = $pdo->prepare("DELETE FROM invoice_lines WHERE invoice_id = :id");
            $lineStmt->execute([':id' => $id]);

            $invStmt = $pdo->prepare("DELETE FROM invoices WHERE id = :id");
            $invStmt->execute([':id' => $id]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e; 
        }
    }
}
