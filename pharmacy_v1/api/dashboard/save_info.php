<?php
require('includes/conn.php');

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $phone_no = trim($_POST['phone_no']);
    $email = trim($_POST['email']);
    $location = trim($_POST['location']);

    if (empty($name) || empty($address) || empty($phone_no) || empty($email) || empty($location) || empty($city)) {
        die("All fields are required.");
    }
    

    $sql = "INSERT INTO info (name, address, city, phone_no, email, location) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die("SQL error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $name, $address, $city, $phone_no, $email, $location);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboad.php?success=info_saved");
        exit();
    } else {
        die("Database insert failed: " . mysqli_stmt_error($stmt));
    }
}
?>
