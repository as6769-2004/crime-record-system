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
<html>
<head>
    <title>Case Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to the custom CSS file -->
    <link rel="stylesheet" href="../../assets/css/case_logs.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center">Case Logs for Officer: <?= $name_safe ?> (Station: <?= $station_name_safe ?>)</h2>

<?php
$query = "
SELECT 
    c.crime_id, c.crime_type, c.crime_date, c.status, c.case_number
FROM CRIME c
WHERE (c.created_by = ? OR c.officer_id = ?) AND c.status = 'open'
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
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";

        // Display case details on the left
        echo "<a href='case_detail.php?crime_id={$row['crime_id']}' class='text-decoration-none'>";
        echo "<h5 class='mb-1'>{$row['crime_type']}</h5>";
        echo "<p class='mb-1'>{$row['status']}</p>";
        echo "<small>Case Number: {$row['case_number']} | Date: {$row['crime_date']}</small>";
        echo "</a>";

        // "Take Action" button on the right
        echo "<button class='btn btn-outline-primary btn-sm ms-2' onclick=\"location.href='case_update.php?crime_id={$row['crime_id']}'\">Take Action</button>";

        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<div class='alert alert-info'>No open cases found for this officer.</div>";
}

$stmt->close();
$conn->close();
?>

</div>
</body>
</html>
