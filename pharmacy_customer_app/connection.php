<?php
// Database credentials
$host = 'localhost';
$username = 'root';     // Default username for XAMPP
$password = '';         // Default password is empty in XAMPP
$dbname = 'swastha';    // Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
