<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInvoiceLinesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('invoice_lines');
        $table->addColumn('invoice_id', 'integer', ['signed' => false])
              ->addColumn('description', 'string', ['limit' => 255])
              ->addColumn('quantity', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
              ->addColumn('price', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
              ->addColumn('total', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0])
              ->addForeignKey('invoice_id', 'invoices', 'id', [
                  'delete' => 'CASCADE',   // if invoice is deleted, remove its lines
                  'update' => 'NO_ACTION' // no action on invoice_id update
              ])
              ->create();
    }
}
