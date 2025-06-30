<?php
include("connection.php");
session_start();

$response = array(); // Initialize response array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = "Invalid email format.";
        echo json_encode($response);
        exit;
    }

    // Prepare and execute query
    $sql = "SELECT id, username, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // Return success response
            $response['success'] = true;
            $response['message'] = "Login successful.";
            $response['user'] = array("username" => $username);
            echo json_encode($response);
            exit;
        } else {
            $response['success'] = false;
            $response['message'] = "Invalid credentials.";
            echo json_encode($response);
            exit;
        }
    } else {
        $response['success'] = false;
        $response['message'] = "No user found with that email.";
        echo json_encode($response);
        exit;
    }
} else {
    // If method is not POST
    echo "Invalid request method.";
}

?>

<h2>User Login</h2>
<form method="post">
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>
