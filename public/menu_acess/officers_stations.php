<?php

// Include your database connection file
include '../../includes/db_connect.php';

// Get station details (replace with your logic to fetch station data)
$station_name = "Your Station Name"; // Example
$station_code = "ST001"; // Example
$station_address = "123 Main St, Anytown"; // Example
$station_id = 1; // Example

// Fetch officer data
$stmt = $conn->prepare("SELECT officer_id, name, badge_number, phone, date_of_hire, email, officer_pic FROM OFFICER WHERE station_id = ?");
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();

$officer_info = "";
if ($result->num_rows > 0) {
    $officer_info .= "<div class='table-responsive'>";
    $officer_info .= "<table class='officer-table'>";
    $officer_info .= "<thead><tr><th>Officer ID</th><th>Name</th><th>Badge Number</th><th>Phone</th><th>Date of Hire</th><th>Email</th><th>Picture</th></tr></thead>";
    $officer_info .= "<tbody>";

    while ($row = $result->fetch_assoc()) {
        $officer_info .= "<tr>";
        $officer_info .= "<td>" . htmlspecialchars($row["officer_id"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["name"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["badge_number"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["phone"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["date_of_hire"]) . "</td>";
        $officer_info .= "<td>" . htmlspecialchars($row["email"]) . "</td>";
        $officer_info .= "<td>";
        if (!empty($row['officer_pic'])) {
            $officer_info .= "<img src='../../assets/pictures/" . htmlspecialchars($row['officer_pic']) . "' alt='Officer Picture' style='max-width: 100px; max-height: 100px;'>";
        }
        $officer_info .= "</td>";
        $officer_info .= "</tr>";
    }
    $officer_info .= "</tbody></table></div>";
} else {
    $officer_info = "<p>No officers found for this station.</p>";
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
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .inner-right-column {
            background-color: #1e1e1e;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            max-width: 90%;
            margin: 20px auto;
            overflow-y: auto;
            max-height: calc(100vh - 100px);
            color: #ffffff;
        }

        .officer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            overflow: hidden;
            color: #ffffff;
        }

        .officer-table th,
        .officer-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #333;
            color: #ffffff;
        }

        .officer-table th {
            background-color: #2a2a2a;
            font-weight: 600;
            color: #ffffff;
        }

        .officer-table tbody tr:hover {
            background-color: #252525;
        }

        .table-responsive {
            overflow-x: auto;
            color: #ffffff;
        }

        .inner-right-column p {
            text-align: center;
            color: #ffffff;
            margin-top: 20px;
        }

        strong {
            color: #ffffff;
        }
    </style>
</head>

<body>
    <div class="inner-right-column">
        <div>
            <strong>Officers:</strong>
            <hr>
            <?php echo $officer_info; ?>
        </div>
    </div>
</body>

</html>