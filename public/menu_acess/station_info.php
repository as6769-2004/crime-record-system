<?php
include('../../includes/db_connect.php');

// Retrieve station name and officer ID from URL
$station_name = isset($_GET['station']) ? urldecode($_GET['station']) : 'Default Station Name'; // Default if not set
$officer_id = isset($_GET['officer_id']) ? intval($_GET['officer_id']) : 0;

// Sanitize inputs
$station_name_safe = htmlspecialchars($station_name);
$officer_id_safe = intval($officer_id);

// Fetch station information from the database
$station_info = null;
$officer_name = 'Unknown Officer';

// Fetch station information
if (!empty($station_name)) {
    $stmt_station = $conn->prepare("SELECT * FROM POLICE_STATION WHERE station_name = ?");
    if (!$stmt_station) {
        $station_info = [ // Default station info if query fails
            'station_id' => 'N/A',
            'station_name' => 'Default Station Name (DB Error)',
            'address' => 'N/A',
            'phone' => 'N/A',
            'precinct' => 'N/A',
            'district' => 'N/A',
            'email' => 'N/A',
            'head_officer_id' => 'N/A'
        ];
    } else {
        $stmt_station->bind_param("s", $station_name_safe);
        if (!$stmt_station->execute()) {
            $station_info = [ // Default station info if execute fails
                'station_id' => 'N/A',
                'station_name' => 'Default Station Name (Execution Error)',
                'address' => 'N/A',
                'phone' => 'N/A',
                'precinct' => 'N/A',
                'district' => 'N/A',
                'email' => 'N/A',
                'head_officer_id' => 'N/A'
            ];
        } else {
            $result_station = $stmt_station->get_result();

            if ($result_station && $result_station->num_rows > 0) {
                $station_info = $result_station->fetch_assoc();
            } else {
                $station_info = [ // Default station info if no rows found
                    'station_id' => 'N/A',
                    'station_name' => 'Default Station Name (No Data)',
                    'address' => 'N/A',
                    'phone' => 'N/A',
                    'precinct' => 'N/A',
                    'district' => 'N/A',
                    'email' => 'N/A',
                    'head_officer_id' => 'N/A'
                ];
            }
        }
        $stmt_station->close();
    }
} else {
    $station_info = [ // Default station info if no station name provided.
        'station_id' => 'N/A',
        'station_name' => 'Default Station Name (No Input)',
        'address' => 'N/A',
        'phone' => 'N/A',
        'precinct' => 'N/A',
        'district' => 'N/A',
        'email' => 'N/A',
        'head_officer_id' => 'N/A'
    ];
}

// Fetch officer name
if ($officer_id_safe > 0) {
    $stmt_officer = $conn->prepare("SELECT name FROM OFFICER WHERE officer_id = ?");
    if (!$stmt_officer) {
        $officer_name = 'Default Officer Name (DB Error)';
    } else {
        $stmt_officer->bind_param("i", $officer_id_safe);
        if (!$stmt_officer->execute()) {
            $officer_name = 'Default Officer Name (Execution Error)';
        } else {
            $result_officer = $stmt_officer->get_result();

            if ($result_officer && $result_officer->num_rows > 0) {
                $row_officer = $result_officer->fetch_assoc();
                $officer_name = htmlspecialchars($row_officer['name']);
            } else {
                $officer_name = 'Default Officer Name (No Data)';
            }
        }
        $stmt_officer->close();
    }
} else {
    $officer_name = 'Default Officer Name (No Input)';
}

$conn->close(); // Close the database connection
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

    <?php if ($station_info !== null) : ?>
        <p><strong>Station ID:</strong> <?php echo isset($station_info['station_id']) ? htmlspecialchars($station_info['station_id']) : 'N/A'; ?></p>
        <p><strong>Station Name:</strong> <?php echo isset($station_info['station_name']) ? htmlspecialchars($station_info['station_name']) : 'N/A'; ?></p>
        <p><strong>Address:</strong> <?php echo isset($station_info['address']) ? htmlspecialchars($station_info['address']) : 'N/A'; ?></p>
        <p><strong>Phone:</strong> <?php echo isset($station_info['phone']) ? htmlspecialchars($station_info['phone']) : 'N/A'; ?></p>
        <p><strong>Precinct:</strong> <?php echo isset($station_info['precinct']) ? htmlspecialchars($station_info['precinct']) : 'N/A'; ?></p>
        <p><strong>District:</strong> <?php echo isset($station_info['district']) ? htmlspecialchars($station_info['district']) : 'N/A'; ?></p>
        <p><strong>Email:</strong> <?php echo isset($station_info['email']) ? htmlspecialchars($station_info['email']) : 'N/A'; ?></p>
        <p><strong>Head Officer ID:</strong> <?php echo isset($station_info['head_officer_id']) ? htmlspecialchars($station_info['head_officer_id']) : 'N/A'; ?></p>
        <p><strong>Logged in Officer:</strong> <?php echo $officer_name; ?></p>
    <?php else : ?>
        <p>Station information not found.</p>
    <?php endif; ?>
</body>

</html>