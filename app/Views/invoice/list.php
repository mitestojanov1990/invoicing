<?php
ob_start();
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">All Invoices</h3>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-hover text-nowrap">
      <thead>
        <tr>
          <th>ID</th>
          <th>Number</th>
          <th>Date</th>
          <th>To</th>
          <th>City</th>
          <th>Type</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($invoices as $inv): ?>
        <tr>
          <td><?= $inv['id'] ?></td>
          <td><?= htmlspecialchars($inv['invoice_number']) ?></td>
          <td><?= $inv['invoice_date'] ?></td>
          <td><?= htmlspecialchars($inv['to_name']) ?></td>
          <td><?= htmlspecialchars($inv['city']) ?></td>
          <td><?= $inv['invoice_type'] ?></td>
          <td>
            <a class="btn btn-sm btn-primary" href="/invoices/<?= $inv['id'] ?>/edit">Edit</a>
            <a class="btn btn-sm btn-danger" href="/invoices/<?= $inv['id'] ?>/delete" onclick="return confirm('Are you sure?')">Delete</a>
            <a class="btn btn-sm btn-secondary" href="/invoices/<?= $inv['id'] ?>/pdf" target="_blank">PDF</a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
