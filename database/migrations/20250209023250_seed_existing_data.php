<?php

declare(strict_types=1);
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Util\Literal;

final class SeedInvoicesData extends AbstractMigration
{
    public function up(): void
    {
        // Insert User
        $this->execute(
            "INSERT INTO users (email, name, password, created_at) 
            VALUES ('taskovs@gmail.com', 'Taskovs', '" . password_hash('P@Ssw0rd', PASSWORD_BCRYPT) . "', NOW())"
        );

        // Retrieve User ID
        $userId = $this->fetchRow("SELECT id FROM users WHERE email = 'taskovs@gmail.com'")['id'];

        // Insert Invoices
        $invoices = [
            ['4/.15', '2015-06-22', 'IMPERIJAL TABAKO', 'VALANDOVO', 1],
            ['5/.15', '2015-06-22', 'GAMAOIL', 'VALANDOVO', 1],
            ['16/15', '2015-12-24', 'ECOTIP', 'Skopje', 1],
            ['15/15', '2015-11-14', 'IMPERIJAL TABAKO', 'Valandovo', 1],
            ['14/15', '2015-11-23', 'GAMAOIL', 'Valandovo', 1],
            ['12/15', '2015-11-02', 'komunalna cistota', 'Bogdanci', 1],
            ['11/15', '2015-09-13', 'Хит Маркет Трејд', 'Штип', 1],
            ['6/15', '2015-06-29', 'MERKJURI', 'Mrzenci', 1],
            ['1/16', '2016-02-03', 'ZORBAS ANDREADIS DOOEL', 'GEVGELIJA', 1],
            ['02/15', '2016-11-03', 'IMPERIJAL TABAKO', 'VALANDOVO', 1],
            ['2/16', '2016-03-29', 'RINA KOM', 'GEVGELIJA', 1],
            ['3/16', '2016-04-04', 'JP KOMUNALNA CISTOTA', 'BOGDANCI', 1],
            ['4/16', '2016-05-02', 'JP KOMUNALNA CISTOTA', 'BOGDANCI', 1],
            ['5/16', '2016-05-02', 'SIBA INTERNACIONAL DBM', 'SKOPJE', 1],
            ['06/16', '2016-08-15', 'JP KOMUNALNA CISTOTA', 'BOGDANCI', 1],
            ['07/2016', '2016-08-22', 'DMD KOMEKS', 'GEVGELIJA', 1],
            ['08/2016', '2016-09-26', 'JP KOMUNALNA CISTOTA', 'BOGDANCI', 1],
            ['09/2016', '2016-10-04', 'IMPERIJAL TABAKO', 'VALANDOVO', 1]
        ];

        foreach ($invoices as $invoice) {
            $this->execute(
                "INSERT INTO invoices (user_id, invoice_number, invoice_date, to_name, city, invoice_type, created_at) 
                VALUES ($userId, '{$invoice[0]}', '{$invoice[1]}', '{$invoice[2]}', '{$invoice[3]}', {$invoice[4]}, NOW())"
            );
        }

        // Insert Invoice Lines
        $invoiceLines = [
            ['2/16', 'Izvrsena usluga ,montaza na vodovod i odvod', 1, 30000, 30000],
            ['3/16', 'Ceino zavaruvanje na f400', 2, 7500, 15000],
            ['3/16', 'Ceva f400 /16 bara (0,5m)', 1, 5000, 5000],
            ['4/16', 'celno zavaruvane na f400', 2, 7500, 15000],
            ['4/16', 'ceva( f400/16bara(0,5m)', 1, 5000, 5000],
            ['5/16', 'Izvrsena usluga promena na plovak', 1, 600, 600],
            ['5/16', 'plovak', 1, 500, 500],
            ['5/16', 'Izvrsena usluga promena na prekidac', 1, 600, 600],
            ['5/16', 'prekidac', 1, 100, 100],
            ['06/16', 'CeIno zavaruvanje f400', 2, 7500, 15000],
            ['06/16', 'Ceva f400/16bara', 1, 5000, 5000],
            ['07/2016', 'Izvrsena usluga', 1, 7200, 7200],
            ['08/2016', 'Celno zavaruvanje f400', 2, 7500, 15000],
            ['08/2016', 'Ceva f400/16 bara', 1, 5000, 5000],
            ['09/2016', 'Dovod za cesma nadvor', 1, 1500, 1500],
            ['09/2016', 'Lepenje plocki (Tutunska Banka)', 1, 500, 500],
            ['09/2016', 'Demontaza na odvod', 1, 500, 500],
            ['09/2016', 'Krsenje so hilt', 1, 1500, 1500],
            ['09/2016', 'Montaza na odvod', 1, 1500, 1500]
        ];

        foreach ($invoiceLines as $line) {
            $invoiceId = $this->fetchRow("SELECT id FROM invoices WHERE invoice_number = '{$line[0]}'")['id'];
            $this->execute(
                "INSERT INTO invoice_lines (invoice_id, description, quantity, price, total) 
                VALUES ($invoiceId, '{$line[1]}', {$line[2]}, {$line[3]}, {$line[4]})"
            );
        }
    }

    public function down(): void
    {
        $this->execute("DELETE FROM invoice_lines WHERE invoice_id IN (SELECT id FROM invoices)");
        $this->execute("DELETE FROM invoices");
        $this->execute("DELETE FROM users WHERE email = 'taskovs@gmail.com'");
    }
}