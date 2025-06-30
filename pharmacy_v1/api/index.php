<?php
require('dashboard/includes/conn.php');
session_start();

// Initialize error message
$error = '';

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "<div class='alert alert-danger'>Please enter both username and password.</div>";
    } else {
        // Secure Query with Prepared Statement
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);

            // Verify hashed password
            if (password_verify($password, $row['password'])) {
                $_SESSION['IS_LOGIN'] = 'yes';
                $_SESSION['sessionId'] = $row['id'];
                $_SESSION['sessionUsername'] = $row['username'];

                // Redirect after successful login
                header('location:dashboard/general_info.php');
                exit();
            } else {
                $error = "<div class='alert alert-danger'>Invalid username or password.</div>";
            }
        } else {
            $error = "<div class='alert alert-danger'>Invalid username or password.</div>";
        }

    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHARMACY</title>
    <link rel="stylesheet" href="dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="dist/css/style.css">
</head>
<body>
    <div class="wrapper">
        <section class="form sign up">
            <center><header> Login</header></center>
            <form class="header"  method="post">
                
                   
                        <div class="field input">
                            <label for="">Username</label>
                            <input type="text" name="username" placeholder="Provide your Username">
                        </div>
                        <div class="field input">
                            <label for="">Password</label>
                            <input type="password" name="password" placeholder="Password">
                       </div>
                       
                        <div class="field button"> 
                            
                            <input type="submit" name="submit" value="LOGIN">
                        </div>
                        <?php echo $error?>
                            </form>
            <p>Register here <a href="register.php">Here</a></p>
        </section>
    </div>
</body>
</html>
