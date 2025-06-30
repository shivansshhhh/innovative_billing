<?php
session_start();
require 'includes/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || !isset($_POST['password'])) {
        echo 'Invalid request.';
        exit;
    }

    $ids = explode(',', $_POST['id']); // 👈 Explode the IDs into array
    $password = $_POST['password'];

    $correctPassword = 'polis'; // ✅ Your admin password

    if ($password !== $correctPassword) {
        echo 'Incorrect password.';
        exit;
    }

    foreach ($ids as $singleId) {
        $singleId = intval($singleId); // sanitize
        
        // Fetch the bill first
        $stmt = $conn->prepare("SELECT * FROM bills WHERE id = ?");
        $stmt->bind_param("i", $singleId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            continue; // If one item not found, skip
        }

        $bill = $result->fetch_assoc();
        $medicineName = $bill['medicine_name'];
        $quantity = $bill['quantity'];

        // Find medicine in stock
        $medStmt = $conn->prepare("SELECT id FROM pharmacy_stock WHERE medicine_name = ?");
        $medStmt->bind_param("s", $medicineName);
        $medStmt->execute();
        $medRes = $medStmt->get_result();

        if ($medRes->num_rows > 0) {
            $medicineId = $medRes->fetch_assoc()['id'];

            // Update stock (add quantity back)
            $updateStmt = $conn->prepare("UPDATE pharmacy_stock SET quantity = quantity + ? WHERE id = ?");
            $updateStmt->bind_param("ii", $quantity, $medicineId);
            $updateStmt->execute();
        }

        // Finally delete the bill item
        $delStmt = $conn->prepare("DELETE FROM bills WHERE id = ?");
        $delStmt->bind_param("i", $singleId);
        $delStmt->execute();
    }

    echo 'success';
}
?>
