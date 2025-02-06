<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('email', 'string', ['limit' => 255])
              ->addColumn('name', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('google_id', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['null' => true])
              ->addIndex(['email'], ['unique' => true])
              ->create();
    }
}
