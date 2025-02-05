<!DOCTYPE html>
<html lang="mk">
<head>
    <meta charset="UTF-8" />
    <title>Invoicing App</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script src="/assets/js/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header>
        <nav>
            <a href="/invoices">Invoices</a>
            <a href="/invoices/create">Create Invoice</a>
        </nav>
    </header>

    <main>
        <!-- Here goes the specific view content -->
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
       <p style="text-align:center;">&copy; <?= date('Y') ?> My Invoicing App</p>
    </footer>
</body>
</html>
