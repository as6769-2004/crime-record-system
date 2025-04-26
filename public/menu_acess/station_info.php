<?php
include('../../includes/db_connect.php');

// Check and validate URL parameters
if (!isset($_GET['station_name']) || !isset($_GET['officer_id']) || !isset($_GET['name'])) {
    $message = urlencode("Missing required parameters. Please login.");
    header("Location: ../start/login.php?error=$message");
    exit;
}

// Retrieve and sanitize parameters
$station_name = urldecode($_GET['station_name']);
$officer_id = intval($_GET['officer_id']);
$officer_name = urldecode($_GET['name']); // updated key

$station_name_safe = htmlspecialchars($station_name);
$officer_name_safe = htmlspecialchars($officer_name);
$officer_id_safe = intval($officer_id);

// Fetch station information
$station_info = [
    'station_id' => 'N/A',
    'station_name' => 'Not Found',
    'address' => 'N/A',
    'phone' => 'N/A',
    'precinct' => 'N/A',
    'district' => 'N/A',
    'email' => 'N/A',
    'head_officer_id' => 'N/A'
];

$stmt_station = $conn->prepare("SELECT * FROM POLICE_STATION WHERE station_name = ?");
if ($stmt_station) {
    $stmt_station->bind_param("s", $station_name_safe);
    if ($stmt_station->execute()) {
        $result_station = $stmt_station->get_result();
        if ($result_station && $result_station->num_rows > 0) {
            $station_info = $result_station->fetch_assoc();
        } else {
            $station_info['station_name'] = "Station Not Found";
        }
    } else {
        $station_info['station_name'] = "Execution Error";
    }
    $stmt_station->close();
} else {
    $station_info['station_name'] = "DB Error";
}

// Fetch officer name from DB (optional override for security)
if ($officer_id_safe > 0) {
    $stmt_officer = $conn->prepare("SELECT name FROM OFFICER WHERE officer_id = ?");
    if ($stmt_officer) {
        $stmt_officer->bind_param("i", $officer_id_safe);
        if ($stmt_officer->execute()) {
            $result_officer = $stmt_officer->get_result();
            if ($result_officer && $result_officer->num_rows > 0) {
                $row_officer = $result_officer->fetch_assoc();
                $officer_name_safe = htmlspecialchars($row_officer['name']); // Override for security
            }
        }
        $stmt_officer->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Station Information</title>
    <link rel="stylesheet" href="../../../crime-record-system/assets/css/station_info.css">
</head>
<body>
    <h1>Station Information</h1>

    <p><strong>Station ID:</strong> <?= htmlspecialchars($station_info['station_id']) ?></p>
    <p><strong>Station Name:</strong> <?= htmlspecialchars($station_info['station_name']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($station_info['address']) ?></p>
    <p><strong>Phone:</strong> <?= htmlspecialchars($station_info['phone']) ?></p>
    <p><strong>Precinct:</strong> <?= htmlspecialchars($station_info['precinct']) ?></p>
    <p><strong>District:</strong> <?= htmlspecialchars($station_info['district']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($station_info['email']) ?></p>
    <p><strong>Head Officer ID:</strong> <?= htmlspecialchars($station_info['head_officer_id']) ?></p>
    <p><strong>Logged in Officer:</strong> <?= $officer_name_safe ?></p>
</body>
</html>
