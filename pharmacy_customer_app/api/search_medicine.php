<?php
header('Content-Type: application/json');

// Connect to database
$conn = new mysqli("localhost", "root", "", "pharmacy_v1");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

// Get search term and user location
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$userLat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$userLng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

if ($search == '' || $userLat === null || $userLng === null) {
    echo json_encode(["error" => "Missing search term or user location."]);
    exit();
}

// Fetch store info
$info_result = $conn->query("SELECT name AS store_name, city, location FROM info LIMIT 1");
$info = $info_result->fetch_assoc();
$store_name = $info['store_name'];
$city = $info['city'];
list($storeLat, $storeLng) = explode(',', $info['location']);
$storeLat = floatval($storeLat);
$storeLng = floatval($storeLng);

// Distance function
function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // km
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    return $earth_radius * $c;
}
$distance = round(calculateDistance($userLat, $userLng, $storeLat, $storeLng), 2);

// Fetch medicines with stock
$query = "
    SELECT id, medicine_name AS name, Qty AS stock, price, expiry_date
    FROM store
    WHERE medicine_name LIKE '%$search%' AND Qty > 0

    UNION

    SELECT id, medicine_name AS name, quantity AS stock, price, expiry_date
    FROM pharmacy_stock
    WHERE medicine_name LIKE '%$search%' AND quantity > 0
";

$result = $conn->query($query);
$medicines = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $row['store_name'] = $store_name;
        $row['city'] = $city;
        $row['distance_km'] = $distance;
        $row['store_lat'] = $storeLat;
        $row['store_lng'] = $storeLng;
        $medicines[] = $row;
    }
}

if (!empty($medicines)) {
    echo json_encode([
        "found" => true,
        "medicines" => $medicines
    ]);
} else {
    // Fallback without stock check
    $alt_query = "
        SELECT id, medicine_name AS name, Qty AS stock, price, expiry_date
        FROM store
        WHERE medicine_name LIKE '%$search%'

        UNION

        SELECT id, medicine_name AS name, quantity AS stock, price, expiry_date
        FROM pharmacy_stock
        WHERE medicine_name LIKE '%$search%'
    ";

    $alt_result = $conn->query($alt_query);
    $alternatives = [];
    if ($alt_result) {
        while ($row = $alt_result->fetch_assoc()) {
            $row['store_name'] = $store_name;
            $row['city'] = $city;
            $row['distance_km'] = $distance;
            $row['store_lat'] = $storeLat;
            $row['store_lng'] = $storeLng;
            $alternatives[] = $row;
        }
    }

    echo json_encode([
        "found" => false,
        "alternatives" => $alternatives
    ]);
}

$conn->close();
?>
