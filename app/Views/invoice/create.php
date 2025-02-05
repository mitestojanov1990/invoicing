<?php
// app/Views/invoice/create.php
ob_start();
?>
<h1>Create Invoice</h1>
<form method="POST" action="/invoices/store" id="create-form">
    <label>Invoice Number:</label>
    <input type="text" name="invoice_number" required /><br/><br/>

    <label>Invoice Date:</label>
    <input type="date" name="invoice_date" value="<?= date('Y-m-d') ?>" /><br/><br/>

    <label>To Name:</label>
    <input type="text" name="to_name" /><br/><br/>

    <label>City:</label>
    <input type="text" name="city" /><br/><br/>

    <label>Type:</label>
    <select name="invoice_type">
        <option value="1">Фактура</option>
        <option value="2">Профактура</option>
        <option value="3">Понуда</option>
    </select>
    <br/><br/>

    <h3>Invoice Lines</h3>
    <table id="lines-table" border="1" cellpadding="5">
       <tr>
         <th>Description</th>
         <th>Quantity</th>
         <th>Price</th>
         <th>Total</th>
         <th>Remove</th>
       </tr>
    </table>
    <button type="button" id="add-line-btn">Add Line</button>

    <!-- We'll store the lines in hidden inputs or post them via JS -->
    <input type="hidden" name="lines" id="lines-json" />

    <br/><br/>
    <button type="submit">Save Invoice</button>
</form>

<script>
const lines = [];

document.getElementById('add-line-btn').addEventListener('click', function() {
    lines.push({ id: 0, description: '', quantity: 0, price: 0, total: 0 });
    renderLines();
});

function renderLines() {
    const tbody = document.getElementById('lines-table').getElementsByTagName('tbody')[0]
        || document.createElement('tbody');
    tbody.innerHTML = '';
    lines.forEach((line, index) => {
        const tr = document.createElement('tr');

        // Description
        let tdDesc = document.createElement('td');
        tdDesc.innerHTML = `<input type="text" data-index="${index}" data-field="description" value="${line.description}" />`;
        tr.appendChild(tdDesc);

        // Quantity
        let tdQty = document.createElement('td');
        tdQty.innerHTML = `<input type="number" step="any" data-index="${index}" data-field="quantity" value="${line.quantity}" />`;
        tr.appendChild(tdQty);

        // Price
        let tdPrice = document.createElement('td');
        tdPrice.innerHTML = `<input type="number" step="any" data-index="${index}" data-field="price" value="${line.price}" />`;
        tr.appendChild(tdPrice);

        // Total
        let tdTotal = document.createElement('td');
        tdTotal.innerText = line.total.toFixed(2);
        tr.appendChild(tdTotal);

        // Remove
        let tdRemove = document.createElement('td');
        tdRemove.innerHTML = `<button type="button" data-index="${index}" class="remove-line">X</button>`;
        tr.appendChild(tdRemove);

        tbody.appendChild(tr);
    });

    if (!document.querySelector('#lines-table tbody')) {
        document.getElementById('lines-table').appendChild(tbody);
    }
    updateHidden();
}

// Event delegation
document.getElementById('lines-table').addEventListener('input', function(e){
    const index = e.target.getAttribute('data-index');
    const field = e.target.getAttribute('data-field');
    if (index !== null && field) {
        lines[index][field] = e.target.value;
        // auto-calc total
        lines[index].total = (lines[index].quantity * lines[index].price) || 0;
        renderLines();
    }
});

document.getElementById('lines-table').addEventListener('click', function(e){
    if (e.target.classList.contains('remove-line')) {
        const index = e.target.getAttribute('data-index');
        lines.splice(index, 1);
        renderLines();
    }
});

function updateHidden() {
    document.getElementById('lines-json').value = JSON.stringify(lines);
}

document.getElementById('create-form').addEventListener('submit', function(e){
    // Convert lines to separate fields or keep as JSON
    // We'll convert them to an array with name="lines[index][field]"
    // But for simplicity, let's keep JSON in hidden input
});

</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
