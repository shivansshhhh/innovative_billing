<?php
session_start();
require "includes/conn.php";
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Generated Bills</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background-color: #f2f2f2; padding: 20px; }
        .container { max-width: 1000px; }
        .table { background: white; }
        h2 { margin-bottom: 30px; }
        .edit-btn, .save-btn { padding: 2px 8px; font-size: 0.875rem; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-primary">All Generated Bills</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Medicine</th>
                    <th>Quantity</th>
                    <th>Total Price</th>
                    <th>Date</th>
                    <th>Bill Amount</th> <!-- New Column -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // First, get all unique bills grouped by customer + billing date
                // First, get all unique bills grouped by customer + billing date
                $sql = "SELECT 
customer_name, 
billing_date, 
GROUP_CONCAT(id) AS ids,
SUM(total_price) AS Bill_amount 
FROM bills 
GROUP BY customer_name, billing_date 
ORDER BY billing_date DESC";

                
                $res = mysqli_query($conn, $sql);

                // Variable to keep track of total amount
                $totalAmount = 0;

                if (mysqli_num_rows($res) > 0) {
                    $i = 1;
                    while ($row = mysqli_fetch_assoc($res)) {
                        $customer = $row['customer_name'];
                        $billing_date = $row['billing_date'];
                        $ids = $row['ids'];
                        $total_amount = $row['Bill_amount']; // Fetch the total amount for the bill

                        // Now fetch all medicines for this grouped bill
                        $medicine_sql = "SELECT * FROM bills WHERE id IN ($ids)";
                        $medicine_res = mysqli_query($conn, $medicine_sql);

                        $medicines = [];
                        $quantities = [];
                        $prices = [];
                        $total_price_sum = 0;

                        while ($med = mysqli_fetch_assoc($medicine_res)) {
                            $medicines[] = htmlspecialchars($med['medicine_name']);
                            $quantities[] = "<span class='view-qty'>{$med['quantity']}</span><input type='number' class='form-control form-control-sm edit-qty d-none' value='{$med['quantity']}'>";
                            $prices[] = "<span class='view-price'>{$med['total_price']} USD</span><input type='text' class='form-control form-control-sm edit-price d-none' value='{$med['total_price']}'>";
                            $total_price_sum += $med['total_price'];
                        }

                        // Add to the total amount
                        $totalAmount += $total_price_sum;

                        echo "<tr data-id='{$ids}'>
                            <td>{$i}</td>
                            <td>".htmlspecialchars($customer)."</td>
                            <td>".implode("<br>", $medicines)."</td>
                            <td>".implode("<br>", $quantities)."</td>
                            <td>".implode("<br>", $prices)."</td>
                            <td>{$billing_date}</td>
                            <td>{$total_amount} USD</td> <!-- This is where the total amount is displayed -->
                            <td>
                                <button class='btn btn-warning btn-sm edit-btn'>Edit</button>
                                <button class='btn btn-success btn-sm save-btn d-none'>Save</button>
                                <button class='btn btn-danger btn-sm delete-btn' data-id='{$ids}'>Delete</button>
                            </td>
                        </tr>";

                        $i++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No bills found</td></tr>";
                }
                ?>
            </tbody>
            <!-- Total amount row -->
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right"><strong>Total Amount</strong></td>
                    <td colspan="3" class="text-left"><strong><?php echo number_format($totalAmount, 2); ?> USD</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

<script>
// Edit button
document.querySelectorAll(".edit-btn").forEach(function(button) {
    button.addEventListener("click", function() {
        const row = this.closest("tr");
        row.querySelectorAll(".view-qty, .view-price").forEach(el => el.classList.add("d-none"));
        row.querySelectorAll(".edit-qty, .edit-price").forEach(el => el.classList.remove("d-none"));
        this.classList.add("d-none");
        row.querySelector(".save-btn").classList.remove("d-none");
    });
});

// Save button
document.querySelectorAll(".save-btn").forEach(function(button) {
    button.addEventListener("click", function() {
        const row = this.closest("tr");
        const ids = row.getAttribute("data-id").split(",");
        const qtys = row.querySelectorAll(".edit-qty");
        const prices = row.querySelectorAll(".edit-price");

        ids.forEach((id, index) => {
            const qty = qtys[index].value;
            const price = prices[index].value;

            fetch("update_bill.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${id}&quantity=${qty}&total_price=${price}`
            })
            .then(res => res.text())
            .then(data => {
                row.querySelectorAll(".view-qty")[index].textContent = qty;
                row.querySelectorAll(".view-price")[index].textContent = price + " USD";
            });
        });

        row.querySelectorAll(".edit-qty, .edit-price").forEach(el => el.classList.add("d-none"));
        row.querySelectorAll(".view-qty, .view-price").forEach(el => el.classList.remove("d-none"));
        this.classList.add("d-none");
        row.querySelector(".edit-btn").classList.remove("d-none");
    });
});

// Delete button
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const ids = this.getAttribute('data-id');
        const password = prompt('Enter password to delete this bill:');
        if (password) {
            fetch('delete_bill.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${encodeURIComponent(ids)}&password=${encodeURIComponent(password)}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    alert('Bill deleted successfully.');
                    location.reload();
                } else {
                    alert(data);
                }
            });
        }
    });
});
</script>
</body>
</html>
