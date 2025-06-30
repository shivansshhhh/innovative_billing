<?php
require "includes/conn.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST["id"]);
    $newQty = intval($_POST["quantity"]);
    $newPrice = floatval($_POST["total_price"]);

    // Step 1: Get existing bill data
    $billQuery = $conn->prepare("SELECT quantity, medicine_id FROM bills WHERE id = ?");
    $billQuery->bind_param("i", $id);
    $billQuery->execute();
    $result = $billQuery->get_result();

    if ($result && $result->num_rows > 0) {
        $bill = $result->fetch_assoc();
        $oldQty = intval($bill["quantity"]);
        $medicineId = intval($bill["medicine_id"]);
        $qtyDiff = $newQty - $oldQty;

        // Step 2: Check current stock
        $stockQuery = $conn->prepare("SELECT quantity FROM pharmacy_stock WHERE id = ?");
        $stockQuery->bind_param("i", $medicineId);
        $stockQuery->execute();
        $stockResult = $stockQuery->get_result();

        if ($stockResult && $stockResult->num_rows > 0) {
            $stock = $stockResult->fetch_assoc();
            $currentStock = intval($stock["quantity"]);

            // Step 3: Validate stock
            if ($currentStock - $qtyDiff < 0) {
                echo "Error: Not enough stock to fulfill this update.";
                exit;
            }

            // Step 4: Update bill
            $updateBill = $conn->prepare("UPDATE bills SET quantity = ?, total_price = ? WHERE id = ?");
            $updateBill->bind_param("idi", $newQty, $newPrice, $id);
            $updateBill->execute();

            // Step 5: Update stock
            $updateStock = $conn->prepare("UPDATE pharmacy_stock SET quantity = quantity - ? WHERE id = ?");
            $updateStock->bind_param("ii", $qtyDiff, $medicineId);
            $updateStock->execute();

            echo "Updated successfully.";
        } else {
            echo "Error: Medicine not found in stock.";
        }
    } else {
        echo "Error: Bill not found.";
    }
} else {
    echo "Invalid request.";
}
?>
