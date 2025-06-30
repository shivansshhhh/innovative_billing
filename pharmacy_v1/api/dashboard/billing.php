<a href="view_bills.php" class="btn btn-secondary mt-3">
    View All Bills <i class="fas fa-file-invoice-dollar ms-1"></i>
</a>
<?php
session_start();
require "includes/conn.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Generate Bill</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css"/>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>

  <!-- Select2 CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

  <style>
    body { background-color: #f8f9fa; }
    .container { max-width: 800px; margin-top: 50px; }
    .card { border-radius: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
    .btn-custom { background-color: #007bff; color: white; border-radius: 25px; }
  </style>
</head>
<body>
<div class="container">
  <div class="card p-4">
    <h2 class="text-center text-primary mb-4">Generate Bill</h2>
    <form action="process_billing.php" method="POST">
      <div class="mb-3">
        <label for="customer_name" class="form-label">Customer Name</label>
        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
      </div>

      <hr>
      <h5 class="text-primary">Add Medicines</h5>
      <div id="medicine-container">
        <div class="medicine-row border rounded p-3 mb-3">
          <div class="row g-2">
            <div class="col-md-5">
              <label class="form-label">Select Medicine</label>
              <select class="form-select medicine-select" name="medicine_id[]" required>
                <option value="" disabled selected>-- Select Medicine --</option>
                <?php
                $query = "SELECT id, medicine_name, price, quantity FROM pharmacy_stock";
                $result = mysqli_query($conn, $query);
                $hasStock = false;
                if ($result && mysqli_num_rows($result) > 0) {
                  while ($row = mysqli_fetch_assoc($result)) {
                    $disabled = $row['quantity'] <= 0 ? 'disabled' : '';
                    $label = htmlspecialchars($row['medicine_name']) . ($row['quantity'] <= 0 ? ' (Out of Stock)' : '');
                    $hasStock = $hasStock || $row['quantity'] > 0;

                    echo "<option value='{$row['id']}' data-price='{$row['price']}' data-name='" . htmlspecialchars($row['medicine_name'], ENT_QUOTES) . "' data-stock='{$row['quantity']}' $disabled>
                          {$label} - {$row['price']} USD (Stock: {$row['quantity']})
                          </option>";
                  }
                } else {
                  echo "<option disabled>No medicines found</option>";
                }
                ?>
              </select>
              <input type="hidden" name="medicine_name[]" class="medicine-name">
            </div>

            <div class="col-md-3">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control quantity-input" name="quantity[]" required min="1">
            </div>

            <div class="col-md-3">
              <label class="form-label">Total Price</label>
              <input type="text" class="form-control price-output" name="total_price[]" readonly>
            </div>

            <div class="col-md-1 d-flex align-items-end">
              <button type="button" class="btn btn-danger remove-medicine w-100">✕</button>
            </div>
          </div>
        </div>
      </div>

      <button type="button" id="add_medicine_btn" class="btn btn-secondary w-100 mb-3" onclick="addMedicine()">+ Add Another Medicine</button>

      <div class="mb-3">
        <label for="billing_date" class="form-label">Date</label>
        <input type="date" class="form-control" id="billing_date" name="billing_date" value="<?php echo date('Y-m-d'); ?>" required>
      </div>

      <div class="mb-3 text-end">
        <label class="form-label fw-bold">Grand Total:</label>
        <input type="text" id="grand_total" class="form-control text-end fw-bold" readonly style="font-size: 1.2rem;">
      </div>

      <button type="submit" class="btn btn-custom w-100">Generate Bill <i class="fas fa-receipt ms-2"></i></button>
    </form>
  </div>
</div>

<script>
  function updateAllPrices() {
    let grandTotal = 0;
    let allStocks = [];

    document.querySelectorAll('.medicine-row').forEach(row => {
      let select = row.querySelector('.medicine-select');
      let quantityInput = row.querySelector('.quantity-input');
      let priceOutput = row.querySelector('.price-output');
      let hiddenName = row.querySelector('.medicine-name');

      let selected = select.selectedOptions[0];
      if (!selected) return;

      let price = parseFloat(selected.getAttribute('data-price')) || 0;
      let name = selected.getAttribute('data-name') || '';
      let stock = parseInt(selected.getAttribute('data-stock')) || 0;
      let qty = parseFloat(quantityInput.value) || 0;

      quantityInput.max = stock;
      allStocks.push(stock);

      if (qty > stock) {
        alert(`Only ${stock} units available for ${name}`);
        quantityInput.value = stock;
        qty = stock;
      }

      let total = price * qty;
      hiddenName.value = name;
      priceOutput.value = total.toFixed(2);
      grandTotal += total;
    });

    document.getElementById('grand_total').value = grandTotal.toFixed(2);

    const canAdd = allStocks.some(stock => stock > 0);
    document.getElementById('add_medicine_btn').disabled = !canAdd;
  }

  function updateMedicineOptions() {
    const allSelects = document.querySelectorAll('.medicine-select');
    const selectedValues = Array.from(allSelects).map(select => select.value).filter(val => val);

    allSelects.forEach(select => {
      const currentValue = select.value;
      Array.from(select.options).forEach(option => {
        if (option.value === "" || option.value === currentValue) {
          option.disabled = false;
        } else {
          option.disabled = selectedValues.includes(option.value);
        }
      });
    });
  }

  function addMedicine() {
    let container = document.getElementById('medicine-container');
    let firstRow = container.querySelector('.medicine-row');
    let newRow = firstRow.cloneNode(true);

    newRow.querySelector('.medicine-select').selectedIndex = 0;
    newRow.querySelector('.quantity-input').value = '';
    newRow.querySelector('.price-output').value = '';
    newRow.querySelector('.medicine-name').value = '';

    container.appendChild(newRow);
    updateMedicineOptions();
  }

  document.addEventListener('change', function (e) {
    if (e.target.matches('.medicine-select') || e.target.matches('.quantity-input')) {
      updateAllPrices();
      updateMedicineOptions();
    }
  });

  document.addEventListener('input', function (e) {
    if (e.target.matches('.quantity-input')) {
      updateAllPrices();
    }
  });

  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-medicine')) {
      let row = e.target.closest('.medicine-row');
      let container = document.getElementById('medicine-container');
      if (container.querySelectorAll('.medicine-row').length > 1) {
        row.remove();
        updateMedicineOptions();
        updateAllPrices();
      }
    }
  });

  const hasStock = <?= $hasStock ? 'true' : 'false' ?>;
  if (!hasStock) {
    document.getElementById('add_medicine_btn').disabled = true;
  }

  // Initialize Select2 for medicine selects
  $(document).ready(function() {
    $('.medicine-select').select2({
      placeholder: '-- Select Medicine --',
      allowClear: true,
      minimumInputLength: 2,  // Start showing results after typing at least 2 characters
      dropdownAutoWidth: true
    });
  });
</script>

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

</body>
</html>
