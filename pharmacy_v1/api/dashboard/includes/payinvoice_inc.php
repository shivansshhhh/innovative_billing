<?php
session_start();
require "conn.php";

if (isset($_POST['submit'])) {
    $qtys = $_POST['qty'];
    $prices = $_POST['price'];
    $totals = $_POST['total'];
    $purchase_no = $_POST['purchase_no'];
    $status = $_POST['status'];
    $date = $_POST['date'];

    $all_success = true;

    for ($i = 0; $i < count($qtys); $i++) {
        $qty = mysqli_real_escape_string($conn, $qtys[$i]);
        $price = mysqli_real_escape_string($conn, $prices[$i]);
        $total = mysqli_real_escape_string($conn, $totals[$i]);

        $sql = "INSERT INTO invoice_pay (qty, price, total, date, purchase_no)
                VALUES ('$qty', '$price', '$total', '$date', '$purchase_no')";
        
        $res = mysqli_query($conn, $sql);
        if (!$res) {
            $all_success = false;
            break;
        }
    }

    if ($all_success) {
        $sql2 = "UPDATE purchase_order SET status = '$status' WHERE purchase_no = '$purchase_no'";
        $res2 = mysqli_query($conn, $sql2);
        if ($res2) {
            $_SESSION['invoice_paid'] = "<div class='alert alert-success'> Paid Successfully</div>";
            header("Location: ../invoice.php");
            exit();
        }
    }

    $_SESSION['invoice_paid_failed'] = "<div class='alert alert-danger'> Failed to pay, please try again</div>";
    header("Location: ../invoice.php");
    exit();
}
?>
