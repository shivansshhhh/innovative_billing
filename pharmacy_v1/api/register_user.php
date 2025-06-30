<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "pharmacy_v1");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

$data = json_decode(file_get_contents("php://input"));

$name = $conn->real_escape_string($data->name);
$phone = $conn->real_escape_string($data->phone);
$password = password_hash($conn->real_escape_string($data->password), PASSWORD_BCRYPT);
$address = $conn->real_escape_string($data->address);

// Check if user already exists
$check = $conn->query("SELECT id FROM users WHERE phone='$phone'");
if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Phone number already registered."]);
    exit();
}

// Insert new user
$result = $conn->query("INSERT INTO users (name, phone, password, address) VALUES ('$name', '$phone', '$password', '$address')");

if ($result) {
    echo json_encode(["success" => true, "message" => "Registration successful!"]);
} else {
    echo json_encode(["success" => false, "message" => "Registration failed."]);
}

$conn->close();
?>
