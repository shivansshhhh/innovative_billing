<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "pharmacy_v1");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$userLat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$userLng = isset($_GET['lng']) ? floatval($_GET['lng']) : null;

if ($search == '' || $userLat === null || $userLng === null) {
    echo json_encode(["error" => "Missing search term or user location."]);
    exit();
}

function haversineFormula($userLat, $userLng, $storeLat, $storeLng) {
    return "
    (6371 * acos(
        cos(radians($userLat)) * cos(radians($storeLat)) *
        cos(radians($storeLng) - radians($userLng)) +
        sin(radians($userLat)) * sin(radians($storeLat))
    ))";
}

$distanceCalc = haversineFormula($userLat, $userLng, "info.latitude", "info.longitude");

$query = "
    SELECT 
        s.id,
        s.medicine_name AS name,
        s.Qty AS stock,
        s.price,
        s.expiry_date,
        info.store_name,
        $distanceCalc AS distance_km
    FROM store s
    JOIN info ON s.pharmacy_id = info.id
    WHERE s.medicine_name LIKE '%$search%' AND s.Qty > 0

    UNION

    SELECT 
        ps.id,
        ps.medicine_name AS name,
        ps.quantity AS stock,
        ps.price,
        ps.expiry_date,
        info.store_name,
        $distanceCalc AS distance_km
    FROM pharmacy_stock ps
    JOIN info ON ps.pharmacy_id = info.id
    WHERE ps.medicine_name LIKE '%$search%' AND ps.quantity > 0
";

$result = $conn->query($query);

$medicines = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $row['distance_km'] = round($row['distance_km'], 2); // round to 2 decimal places
        $medicines[] = $row;
    }
}

if (!empty($medicines)) {
    echo json_encode([
        "found" => true,
        "medicines" => $medicines
    ]);
} else {
    // Same logic for alternatives
    $alt_query = "
        SELECT 
            s.id,
            s.medicine_name AS name,
            s.Qty AS stock,
            s.price,
            s.expiry_date,
            info.store_name,
            $distanceCalc AS distance_km
        FROM store s
        JOIN info ON s.pharmacy_id = info.id
        WHERE s.medicine_name LIKE '%$search%'

        UNION

        SELECT 
            ps.id,
            ps.medicine_name AS name,
            ps.quantity AS stock,
            ps.price,
            ps.expiry_date,
            info.store_name,
            $distanceCalc AS distance_km
        FROM pharmacy_stock ps
        JOIN info ON ps.pharmacy_id = info.id
        WHERE ps.medicine_name LIKE '%$search%'
    ";

    $alt_result = $conn->query($alt_query);
    $alternatives = [];
    if ($alt_result) {
        while($row = $alt_result->fetch_assoc()) {
            $row['distance_km'] = round($row['distance_km'], 2);
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
