<?php
// app/Models/InvoiceLine.php
namespace App\Models;

use PDO;
use Exception;

require_once __DIR__ . '/../../config/database.php';

class InvoiceLine
{
    public int $id;
    public int $invoice_id;
    public string $description;
    public float $quantity;
    public float $price;
    public float $total;

    public static function create(array $data): int
    {
        $pdo = getPDO();
        $sql = "INSERT INTO invoice_lines 
                (invoice_id, description, quantity, price, total) 
                VALUES (:inv, :desc, :qty, :price, :total)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':inv'   => $data['invoice_id'],
            ':desc'  => $data['description'],
            ':qty'   => $data['quantity'],
            ':price' => $data['price'],
            ':total' => $data['total'],
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function findByInvoice(int $invoiceId): array
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("SELECT * FROM invoice_lines WHERE invoice_id = :inv");
        $stmt->execute([':inv' => $invoiceId]);
        return $stmt->fetchAll();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = getPDO();
        $sql = "UPDATE invoice_lines
                SET description = :desc,
                    quantity = :qty,
                    price = :price,
                    total = :total
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':desc'  => $data['description'],
            ':qty'   => $data['quantity'],
            ':price' => $data['price'],
            ':total' => $data['total'],
            ':id'    => $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = getPDO();
        $stmt = $pdo->prepare("DELETE FROM invoice_lines WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
