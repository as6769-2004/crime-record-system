<?php

// Include your database connection file
include '../../includes/db_connect.php';

// Get station details (replace with your logic to fetch station data)
$station_name = "Your Station Name"; // Example
$station_code = "ST001"; // Example
$station_address = "123 Main St, Anytown"; // Example
$station_id = 1; // Example

// Use a PREPARED STATEMENT to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM Officer WHERE Station_ID = ?");
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();

$officer_info = "";
if ($result->num_rows > 0) {
    $officer_info .= "<table border='1'>"; // Use a table for better arrangement
    $officer_info .= "<thead><tr><th>Name</th><th>Rank</th><th>Post</th><th>DOB</th><th>Contact</th><th>Email</th></tr></thead>"; // Table header
    $officer_info .= "<tbody>"; // Start table body

    while ($row = $result->fetch_assoc()) {
        $officer_info .= "<tr>";
        $officer_info .= "<td>" . htmlspecialchars($row["Name"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["Post"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["DOB"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["ContactNo"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["Email"]) . "</td>";
        $officer_info .= "</tr>";
    }
    $officer_info .= "</tbody></table>"; // Close table body and table
} else {
    $officer_info = "No officers found for this station.";
}

$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($station_name); ?> - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/officers_station.css">
    <style>

    </style>
</head>

<body>


    <div class="inner-right-column">
        <div>
            <ul>
                <li><strong>Officers:</strong><hr> <?php echo $officer_info; ?></li>

            </ul>
            
        </div>
    </div>

</body>

</html>