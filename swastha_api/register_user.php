<?php
include 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_BCRYPT);
$phone = $data['phone'];
$address = $data['address'];
$latitude = $data['latitude'];
$longitude = $data['longitude'];

$sql = "INSERT INTO users (name, email, password, phone_no, address, latitude, longitude)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $name, $email, $password, $phone, $address, $latitude, $longitude);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User registered"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>
