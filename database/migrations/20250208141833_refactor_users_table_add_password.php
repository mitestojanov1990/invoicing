<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class RefactorUsersTableAddPassword extends AbstractMigration
{
    public function change(): void
    {
        // Get the 'users' table
        $table = $this->table('users');

        // Check if the column 'password' does not already exist (optional safeguard)
        if (!$table->hasColumn('password')) {
            // Add the 'password' column (nullable, since Google users might not have one)
            $table->addColumn('password', 'string', [
                'limit'   => 255,
                'null'    => true,
                'default' => null,
                'comment' => 'Hashed password for email registration'
            ])
            ->update();
        }
    }
}
