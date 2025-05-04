<?php
include '../../includes/db_connect.php';

$station_name = $_GET['station_name'] ?? '';
$officer_id = $_GET['officer_id'] ?? 0;
$name = $_GET['name'] ?? '';

$station_name_safe = htmlspecialchars($station_name);
$name_safe = htmlspecialchars($name);
$officer_id = intval($officer_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Status</title>
    <link rel="stylesheet" href="../../assets/css/case_status.css">
</head>
<body>
<div class="container">
    <header class="text-center mb-4">
        <h1>Case Status Overview</h1>
        <h3>Officer: <?= $name_safe ?> (Station: <?= $station_name_safe ?>)</h3>
    </header>

<?php
$query = "
SELECT 
    c.crime_id, c.crime_type, c.crime_date, c.location, c.description, c.status, c.case_number, c.reported_at,
    o.name AS officer_name, o.badge_number, o.r_rank, o.phone AS officer_phone, o.email AS officer_email,
    IFNULL(v.name, 'Not Added') AS victim_name, IFNULL(v.address, 'Not Added') AS victim_address,
    IFNULL(v.phone, 'Not Added') AS victim_phone, IFNULL(v.email, 'Not Added') AS victim_email,
    IFNULL(v.gender, 'Not Added') AS victim_gender, IFNULL(v.date_of_birth, 'Not Added') AS victim_dob,
    IFNULL(v.victim_pic, 'Not Added') AS victim_pic,
    IFNULL(s.name, 'Not Added') AS suspect_name, IFNULL(s.address, 'Not Added') AS suspect_address,
    IFNULL(s.phone, 'Not Added') AS suspect_phone, IFNULL(s.gender, 'Not Added') AS suspect_gender,
    IFNULL(s.date_of_birth, 'Not Added') AS suspect_dob, IFNULL(s.suspect_pic, 'Not Added') AS suspect_pic,
    IFNULL(s.known_offender, 'Not Added') AS suspect_known_offender
FROM CRIME c
LEFT JOIN OFFICER o ON c.officer_id = o.officer_id
LEFT JOIN VICTIM v ON c.victim_id = v.victim_id
LEFT JOIN SUSPECT s ON c.suspect_id = s.suspect_id
WHERE c.created_by = ? OR c.officer_id = ?
ORDER BY c.crime_date DESC;
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("<div class='alert alert-danger'>Prepare failed: " . $conn->error . "</div>");
}
$stmt->bind_param("ii", $officer_id, $officer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='card mb-4 shadow'>";
        echo "<div class='card-header'>";
        echo "<h5>Case #{$row['case_number']} - {$row['crime_type']}</h5>";
        echo "<small>Status: {$row['status']} | Date: {$row['crime_date']}</small>";
        echo "</div>";
        echo "<div class='card-body'>";
        
        echo "<div class='crime-details'>";
        echo "<h6>Crime Details</h6>";
        echo "<p><strong>Location:</strong> {$row['location']}<br>";
        echo "<strong>Description:</strong> {$row['description']}<br>";
        echo "<strong>Reported At:</strong> {$row['reported_at']}</p>";
        echo "</div>";

        echo "<div class='officer-details'>";
        echo "<h6>Officer Handling Case</h6>";
        echo "<p><strong>Name:</strong> {$row['officer_name']}<br><strong>Rank:</strong> {$row['r_rank']}<br><strong>Badge #:</strong> {$row['badge_number']}<br><strong>Phone:</strong> {$row['officer_phone']}<br><strong>Email:</strong> {$row['officer_email']}</p>";
        echo "</div>";

        echo "<div class='person-details'>";
        echo "<h6>Victim Information</h6>";
        echo "<p><strong>Name:</strong> {$row['victim_name']}<br><strong>Address:</strong> {$row['victim_address']}<br><strong>Phone:</strong> {$row['victim_phone']}<br><strong>Email:</strong> {$row['victim_email']}<br><strong>Gender:</strong> {$row['victim_gender']}<br><strong>DOB:</strong> {$row['victim_dob']}</p>";
        if ($row['victim_pic'] !== 'Not Added') {
            echo "<img src='../uploads/{$row['victim_pic']}' alt='Victim Photo' class='img-thumbnail'>";
        } else {
            echo "Photo: Not Added";
        }
        echo "</div>";

        echo "<div class='person-details'>";
        echo "<h6>Suspect Information</h6>";
        echo "<p><strong>Name:</strong> {$row['suspect_name']}<br><strong>Address:</strong> {$row['suspect_address']}<br><strong>Phone:</strong> {$row['suspect_phone']}<br><strong>Gender:</strong> {$row['suspect_gender']}<br><strong>DOB:</strong> {$row['suspect_dob']}<br><strong>Known Offender:</strong> {$row['suspect_known_offender']}</p>";
        if ($row['suspect_pic'] !== 'Not Added') {
            echo "<img src='../uploads/{$row['suspect_pic']}' alt='Suspect Photo' class='img-thumbnail'>";
        } else {
            echo "Photo: Not Added";
        }
        echo "</div>";

        $log_query = "SELECT log_date, log_entry FROM CASE_LOGS WHERE crime_id = ?";
        $log_stmt = $conn->prepare($log_query);
        $log_stmt->bind_param("i", $row['crime_id']);
        $log_stmt->execute();
        $logs = $log_stmt->get_result();

        echo "<div class='case-logs'>";
        echo "<h6>Case Logs</h6>";
        if ($logs->num_rows > 0) {
            echo "<ul class='list-group'>";
            while ($log = $logs->fetch_assoc()) {
                echo "<li class='list-group-item'><strong>{$log['log_date']}:</strong> {$log['log_entry']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No logs added.</p>";
        }
        echo "</div>";

        $evidence_query = "SELECT description, location_found, date_collected, file_url FROM EVIDENCE WHERE crime_id = ?";
        $evi_stmt = $conn->prepare($evidence_query);
        $evi_stmt->bind_param("i", $row['crime_id']);
        $evi_stmt->execute();
        $evidences = $evi_stmt->get_result();

        echo "<div class='evidence'>";
        echo "<h6>Evidence</h6>";
        if ($evidences->num_rows > 0) {
            while ($evi = $evidences->fetch_assoc()) {
                echo "<div class='evidence-item'>";
                echo "<strong>Description:</strong> {$evi['description']}<br>";
                echo "<strong>Location Found:</strong> {$evi['location_found']}<br>";
                echo "<strong>Date Collected:</strong> {$evi['date_collected']}<br>";
                if (!empty($evi['file_url'])) {
                    echo "<a href='../uploads/{$evi['file_url']}' target='_blank' class='btn btn-sm btn-outline-primary'>View File</a>";
                } else {
                    echo "No file uploaded.";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No evidence added.</p>";
        }
        echo "</div>";
        echo "</div></div>";
    }
} else {
    echo "<div class='alert alert-info'>No cases found for this officer.</div>";
}

$stmt->close();
$conn->close();
?>

</div>
</body>
</html>
