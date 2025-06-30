<?php
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli("localhost", "root", "", "pharmacy_v1");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

// Get all medicines from both tables
$query = "
    SELECT id, medicine_name AS name, Qty AS stock, price, expiry_date
    FROM store
    WHERE Qty > 0

    UNION

    SELECT id, medicine_name AS name, pharmacy_Qty AS stock, price, expiry_date
    FROM pharmacy_stock
    WHERE pharmacy_Qty > 0
";

$result = $conn->query($query);

$medicines = [];

if ($result) {
    while($row = $result->fetch_assoc()) {
        $medicines[] = $row;
    }
    echo json_encode($medicines);
} else {
    echo json_encode(["error" => "Query Failed"]);
}

$conn->close();
?>
