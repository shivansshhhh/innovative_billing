<?php
session_start();
require "includes/conn.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'] ?? '';
    $medicine_ids = $_POST['medicine_id'] ?? [];    // corrected
    $medicine_names = $_POST['medicine_name'] ?? []; // corrected
    $quantities = $_POST['quantity'] ?? [];          // corrected
    $total_prices = $_POST['total_price'] ?? [];     // corrected
    $billing_date = $_POST['billing_date'] ?? '';

    if (is_array($billing_date)) {
        $billing_date = implode(', ', $billing_date);
    }

    $insert_sql = "INSERT INTO bills (customer_name, medicine_id, medicine_name, quantity, total_price, billing_date)
                   VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);

    foreach ($medicine_ids as $index => $medicine_id) {
        $medicine_name = $medicine_names[$index] ?? '';
        $quantity = (int) ($quantities[$index] ?? 0);
        $total_price = (float) ($total_prices[$index] ?? 0);

        // 1. Insert the bill
        $stmt->bind_param("sissds", $customer_name, $medicine_id, $medicine_name, $quantity, $total_price, $billing_date);
        $stmt->execute();

        // 2. Update the stock
        $update_stock_sql = "UPDATE pharmacy_stock SET quantity = GREATEST(quantity - ?, 0) WHERE id = ?";
        $update_stmt = $conn->prepare($update_stock_sql);
        $update_stmt->bind_param("ii", $quantity, $medicine_id);
        $update_stmt->execute();
    }
} // <-- properly closing the main if

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .invoice-box {
            background: white;
            padding: 40px;
            max-width: 800px;
            margin: auto;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .invoice-header h1 {
            color: #007bff;
            margin-bottom: 0;
        }
        .invoice-header small {
            color: #666;
        }
        .bill-to {
            margin-bottom: 30px;
        }
        .invoice-details th, .invoice-details td {
            padding: 10px;
        }
        .invoice-details th {
            background-color: #f1f1f1;
        }
        .total-row {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 0.9rem;
            color: #666;
        }
        .btn-print, .btn-back {
            border-radius: 25px;
            padding: 10px 20px;
        }
        .btn-print {
            background-color: #28a745;
            color: white;
        }
        .btn-back {
            background-color: #007bff;
            color: white;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="invoice-header">
            <h1>Sunrise Pharmacy</h1>
            <small>123 Health Ave, MedCity | Phone: +1-800-123-4567</small>
        </div>

        <div class="bill-to">
            <p><strong>Customer:</strong> <?php echo htmlspecialchars((string)$customer_name); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars((string)$billing_date); ?></p>
        </div>

        <table class="table invoice-details">
    <thead>
        <tr>
            <th>Medicine Name</th>
            <th>Quantity</th>
            <th>Total Price (USD)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = 0; // to sum up the total
        foreach ($medicine_names as $index => $med_name) {
            $qty = (int)($quantities[$index] ?? 0);
            $price = (float)($total_prices[$index] ?? 0);
            $grand_total += $price;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($med_name); ?></td>
                <td><?php echo htmlspecialchars($qty); ?></td>
                <td><?php echo htmlspecialchars(number_format($price, 2)); ?></td>
            </tr>
        <?php } ?>
        
        <tr class="total-row">
            <td colspan="2" class="text-end">Amount Paid:</td>
            <td><strong><?php echo htmlspecialchars(number_format($grand_total, 2)); ?> USD</strong></td>
        </tr>
    </tbody>
</table>


        <div class="footer-note">
            Thank you for choosing Sunrise Pharmacy. Get well soon!
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print Invoice
            </button>
            <a href="billing.php" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Go to Billing
            </a>
        </div>
    </div>
</body>
</html>
