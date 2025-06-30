<?php
session_start();
require "conn.php";

if (isset($_POST['submit'])) {
    $supplier_name = trim($_POST['supplier_name']);

    // Prepared statement for security
    $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name) VALUES (?)");
    $stmt->bind_param("s", $supplier_name);

    if ($stmt->execute()) {
        $_SESSION['success'] = "<div class='alert alert-success'>Added Successfully</div>";
    } else {
        $_SESSION['failed'] = "<div class='alert alert-danger'>Failed to Add</div>";
    }

    $stmt->close();
    header("Location: ../suppliers.php");
    exit();
}
?>
