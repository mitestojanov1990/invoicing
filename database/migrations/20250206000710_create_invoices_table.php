<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateInvoicesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('invoices');
        $table->addColumn('user_id', 'integer', ['signed' => false])
              ->addColumn('invoice_number', 'string', ['limit' => 50])
              ->addColumn('invoice_date', 'date')
              ->addColumn('to_name', 'string', ['limit' => 255])
              ->addColumn('city', 'string', ['limit' => 255])
              ->addColumn('invoice_type', 'integer', ['default' => 1])
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addForeignKey('user_id', 'users', 'id', [
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION'
              ])
              ->create();
    }
}
