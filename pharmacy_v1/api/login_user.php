<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "pharmacy_v1");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

$data = json_decode(file_get_contents("php://input"));

$phone = $conn->real_escape_string($data->phone);
$password = $conn->real_escape_string($data->password);

// Find user
$result = $conn->query("SELECT * FROM users WHERE phone='$phone'");

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    
    if (password_verify($password, $user['password'])) {
        // Login successful
        echo json_encode([
            "success" => true,
            "user" => [
                "id" => $user['id'],
                "name" => $user['name'],
                "phone" => $user['phone'],
                "address" => $user['address']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Incorrect password."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "User not found."]);
}

$conn->close();
?>
