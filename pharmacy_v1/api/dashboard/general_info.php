<?php 
require('includes/conn.php');
$sql="select * from info";

$res=mysqli_query($conn,$sql);
$count= mysqli_num_rows($res);
if($count > 0){
    $row=mysqli_fetch_assoc($res);
    header('location:dashboad.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHARMACY</title>
    <link rel="stylesheet" href="../dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../dist/css/style.css">
</head>
<body>
    <div class="wrapper">
        <section class="form sign up">
            <center><header> Pharmacy Set Up</header></center>
            <form action="save_info.php" class="header" method="post">
                
                <div class="field input">
                    <label for="">Pharmacy Name</label>
                    <input type="text" name="name" placeholder="Input pharmacy name" required>
                </div>
                <div class="field input">
                    <label for="">Address</label>
                    <input type="text" name="address" placeholder="Input the address" required>
                </div>
                <div class="field input">
    <label for="">City</label>
    <input type="text" name="city" placeholder="Input city" required>
</div>


                <div class="field input">
                    <label for="">Phone Number</label>
                    <input type="text" name="phone_no" placeholder="Input phone number" required>
                </div>
                <div class="field input">
                    <label for="">Email</label>
                    <input type="email" name="email" placeholder="Input the pharmacy's email" required>
                </div>

                <!-- Hidden input to store location value -->
                <input type="hidden" name="location" id="location">

                <!-- Displayed location field (read-only) -->
                <div class="field input">
                    <label for="">Location (auto or manual)</label>
                    <input type="text" id="locationDisplay" placeholder="Detecting location..." disabled>
                </div>

                <!-- Manual fallback input, hidden unless needed -->
                <div class="field input" id="manualLocationField" style="display:none;">
                    <label for="">Enter Coordinates Manually (lat,lon)</label>
                    <input type="text" id="manualLocationInput" placeholder="e.g., 6.5244,3.3792">
                </div>
                
                <div class="field button"> 
                    <input type="submit" name="submit" value="Continue">
                </div>
            </form>
        </section>
    </div>

    <!-- Location script -->
    <script>
    window.onload = function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    const coords = lat + ',' + lon;
                    document.getElementById('location').value = coords;
                    document.getElementById('locationDisplay').value = coords;
                },
                function(error) {
                    document.getElementById('locationDisplay').value = "Auto-location failed. Enter manually.";
                    document.getElementById('manualLocationField').style.display = "block";

                    document.getElementById('manualLocationInput').addEventListener('input', function () {
                        document.getElementById('location').value = this.value;
                    });
                }
            );
        } else {
            document.getElementById('locationDisplay').value = "Geolocation not supported. Enter manually.";
            document.getElementById('manualLocationField').style.display = "block";
        }
    };
    </script>
</body>
</html>
