<?php
// app/Views/invoice/edit.php
ob_start();
?>
<h1>Edit Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h1>
<form method="POST" action="/invoices/<?= $invoice['id'] ?>/update" id="edit-form">
    <label>Invoice Number:</label>
    <input type="text" name="invoice_number" value="<?= htmlspecialchars($invoice['invoice_number']) ?>" /><br/><br/>

    <label>Invoice Date:</label>
    <input type="date" name="invoice_date" value="<?= $invoice['invoice_date'] ?>" /><br/><br/>

    <label>To Name:</label>
    <input type="text" name="to_name" value="<?= htmlspecialchars($invoice['to_name']) ?>" /><br/><br/>

    <label>City:</label>
    <input type="text" name="city" value="<?= htmlspecialchars($invoice['city']) ?>" /><br/><br/>

    <label>Type:</label>
    <select name="invoice_type">
        <option value="1" <?= $invoice['invoice_type']==1?'selected':'' ?>>Фактура</option>
        <option value="2" <?= $invoice['invoice_type']==2?'selected':'' ?>>Профактура</option>
        <option value="3" <?= $invoice['invoice_type']==3?'selected':'' ?>>Понуда</option>
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

    <input type="hidden" name="lines" id="lines-json" />
    <br/><br/>
    <button type="submit">Update Invoice</button>
</form>

<script>
let lines = <?= json_encode($lines) ?>; 

function renderLines() {
    const tbody = document.createElement('tbody');
    lines.forEach((line, index) => {
        const tr = document.createElement('tr');

        let tdDesc = document.createElement('td');
        tdDesc.innerHTML = `<input type="text" data-index="${index}" data-field="description" value="${line.description}" />`;
        tr.appendChild(tdDesc);

        let tdQty = document.createElement('td');
        tdQty.innerHTML = `<input type="number" step="any" data-index="${index}" data-field="quantity" value="${line.quantity}" />`;
        tr.appendChild(tdQty);

        let tdPrice = document.createElement('td');
        tdPrice.innerHTML = `<input type="number" step="any" data-index="${index}" data-field="price" value="${line.price}" />`;
        tr.appendChild(tdPrice);

        let tdTotal = document.createElement('td');
        tdTotal.innerText = parseFloat(line.total).toFixed(2);
        tr.appendChild(tdTotal);

        let tdRemove = document.createElement('td');
        tdRemove.innerHTML = `<button type="button" data-index="${index}" class="remove-line">X</button>`;
        tr.appendChild(tdRemove);

        tbody.appendChild(tr);
    });

    const table = document.getElementById('lines-table');
    table.querySelector('tbody')?.remove();
    table.appendChild(tbody);
    updateHidden();
}

document.getElementById('lines-table').addEventListener('input', function(e){
    const index = e.target.getAttribute('data-index');
    const field = e.target.getAttribute('data-field');
    if (index !== null && field) {
        lines[index][field] = e.target.value;
        lines[index].total = lines[index].quantity * lines[index].price;
        renderLines();
    }
});

document.getElementById('lines-table').addEventListener('click', function(e){
    if(e.target.classList.contains('remove-line')) {
        const index = e.target.getAttribute('data-index');
        lines.splice(index, 1);
        renderLines();
    }
});

document.getElementById('add-line-btn').addEventListener('click', function(){
    lines.push({ id: 0, description: '', quantity: 0, price: 0, total: 0 });
    renderLines();
});

function updateHidden() {
    document.getElementById('lines-json').value = JSON.stringify(lines);
}

renderLines();
</script>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
