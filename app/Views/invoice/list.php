<?php 
// app/Views/invoice/list.php
ob_start(); 
?>
<h1>All Invoices</h1>
<table border="1" cellspacing="0" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Number</th>
        <th>Date</th>
        <th>To</th>
        <th>City</th>
        <th>Type</th>
        <th>Actions</th>
    </tr>
    <?php foreach($invoices as $inv): ?>
    <tr>
        <td><?= $inv['id'] ?></td>
        <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
        <td><?= $inv['invoice_date'] ?></td>
        <td><?= htmlspecialchars($inv['to_name']) ?></td>
        <td><?= htmlspecialchars($inv['city']) ?></td>
        <td><?= $inv['invoice_type'] ?></td>
        <td>
            <a href="/invoices/<?= $inv['id'] ?>/edit">Edit</a> | 
            <a href="/invoices/<?= $inv['id'] ?>/delete" 
               onclick="return confirm('Are you sure?')">Delete</a> |
            <a href="/invoices/<?= $inv['id'] ?>/pdf" target="_blank">PDF</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php'; // or echo $content if not using a layout
