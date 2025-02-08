<?php
ob_start();
?>
<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Create Invoice</h3>
  </div>
  <!-- /.card-header -->
  <!-- form start -->
  <form method="POST" action="/invoices/store" id="create-form">
    <div class="card-body">
      <div class="form-group">
        <label for="invoice_number">Invoice Number</label>
        <input type="text" class="form-control" name="invoice_number" id="invoice_number" required>
      </div>
      <div class="form-group">
        <label for="invoice_date">Invoice Date</label>
        <input type="date" class="form-control" name="invoice_date" id="invoice_date" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="form-group">
        <label for="to_name">To Name</label>
        <input type="text" class="form-control" name="to_name" id="to_name">
      </div>
      <div class="form-group">
        <label for="city">City</label>
        <input type="text" class="form-control" name="city" id="city">
      </div>
      <div class="form-group">
        <label for="invoice_type">Type</label>
        <select class="form-control" name="invoice_type" id="invoice_type">
          <option value="1">Фактура</option>
          <option value="2">Профактура</option>
          <option value="3">Понуда</option>
        </select>
      </div>

      <h5 class="mt-4">Invoice Lines</h5>
      <div class="table-responsive">
        <table class="table table-bordered" id="lines-table">
          <thead>
            <tr>
              <th>Description</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
              <th>Remove</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic content will be injected here -->
          </tbody>
        </table>
      </div>
      <button type="button" class="btn btn-secondary mt-2" id="add-line-btn">Add Line</button>

      <input type="hidden" name="lines" id="lines-json" />
    </div>
    <!-- /.card-body -->

    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Save Invoice</button>
    </div>
  </form>
</div>
<!-- /.card -->

<script>
  const lines = [];

  document.getElementById('add-line-btn').addEventListener('click', function() {
      lines.push({ id: 0, description: '', quantity: 0, price: 0, total: 0 });
      renderLines();
  });

  function renderLines() {
      const tbody = document.querySelector('#lines-table tbody');
      tbody.innerHTML = '';
      lines.forEach((line, index) => {
          const tr = document.createElement('tr');

          let tdDesc = document.createElement('td');
          tdDesc.innerHTML = `<input type="text" class="form-control" data-index="${index}" data-field="description" value="${line.description}" />`;
          tr.appendChild(tdDesc);

          let tdQty = document.createElement('td');
          tdQty.innerHTML = `<input type="number" step="any" class="form-control" data-index="${index}" data-field="quantity" value="${line.quantity}" />`;
          tr.appendChild(tdQty);

          let tdPrice = document.createElement('td');
          tdPrice.innerHTML = `<input type="number" step="any" class="form-control" data-index="${index}" data-field="price" value="${line.price}" />`;
          tr.appendChild(tdPrice);

          let tdTotal = document.createElement('td');
          tdTotal.innerText = parseFloat(line.total).toFixed(2);
          tr.appendChild(tdTotal);

          let tdRemove = document.createElement('td');
          tdRemove.innerHTML = `<button type="button" class="btn btn-danger btn-sm remove-line" data-index="${index}"><i class="fas fa-trash"></i></button>`;
          tr.appendChild(tdRemove);

          tbody.appendChild(tr);
      });
      updateHidden();
  }

  document.getElementById('lines-table').addEventListener('input', function(e){
      const index = e.target.getAttribute('data-index');
      const field = e.target.getAttribute('data-field');
      if (index !== null && field) {
          lines[index][field] = e.target.value;
          lines[index].total = (lines[index].quantity * lines[index].price) || 0;
          renderLines();
      }
  });

  document.getElementById('lines-table').addEventListener('click', function(e){
      if (e.target.closest('.remove-line')) {
          const index = e.target.closest('.remove-line').getAttribute('data-index');
          lines.splice(index, 1);
          renderLines();
      }
  });

  function updateHidden() {
      document.getElementById('lines-json').value = JSON.stringify(lines);
  }
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
