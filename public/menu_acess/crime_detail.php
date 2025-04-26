<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);

$query = "
SELECT 
    c.crime_id, c.crime_type, c.crime_date, c.location, c.description, c.status, c.case_number, c.reported_at,
    
    -- Officer
    o.name AS officer_name, o.badge_number, o.r_rank, o.phone AS officer_phone, o.email AS officer_email,
    
    -- Victim
    IFNULL(v.name, 'Not Added') AS victim_name, IFNULL(v.address, 'Not Added') AS victim_address,
    IFNULL(v.phone, 'Not Added') AS victim_phone, IFNULL(v.email, 'Not Added') AS victim_email,
    IFNULL(v.gender, 'Not Added') AS victim_gender, IFNULL(v.date_of_birth, 'Not Added') AS victim_dob,
    IFNULL(v.victim_pic, 'Not Added') AS victim_pic,

    -- Suspect
    IFNULL(s.name, 'Not Added') AS suspect_name, IFNULL(s.address, 'Not Added') AS suspect_address,
    IFNULL(s.phone, 'Not Added') AS suspect_phone, IFNULL(s.gender, 'Not Added') AS suspect_gender,
    IFNULL(s.date_of_birth, 'Not Added') AS suspect_dob, IFNULL(s.suspect_pic, 'Not Added') AS suspect_pic,
    IFNULL(s.known_offender, 'Not Added') AS suspect_known_offender

FROM CRIME c
LEFT JOIN OFFICER o ON c.officer_id = o.officer_id
LEFT JOIN VICTIM v ON c.victim_id = v.victim_id
LEFT JOIN SUSPECT s ON c.suspect_id = s.suspect_id
WHERE c.crime_id = ?;
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("<div class='alert alert-danger'>Prepare failed: " . $conn->error . "</div>");
}
$stmt->bind_param("i", $crime_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Case Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Link to the custom CSS file -->
    <link rel="stylesheet" href="../menu_acess/styles.css">
</head>
<body>
<div class="container py-4">
    <h2 class="mb-4 text-center">Case Details for Crime #<?= $row['case_number'] ?> (<?= $row['crime_type'] ?>)</h2>

    <div class="card mb-4 shadow">
        <div class="card-header bg-dark text-white">
            <h5>Crime Details</h5>
        </div>
        <div class="card-body">
            <p><strong>Location:</strong> <?= $row['location'] ?><br>
            <strong>Description:</strong> <?= $row['description'] ?><br>
            <strong>Status:</strong> <?= $row['status'] ?><br>
            <strong>Reported At:</strong> <?= $row['reported_at'] ?></p>

            <h6>Officer Handling Case</h6>
            <p>Name: <?= $row['officer_name'] ?><br>Rank: <?= $row['r_rank'] ?><br>Badge #: <?= $row['badge_number'] ?><br>Phone: <?= $row['officer_phone'] ?><br>Email: <?= $row['officer_email'] ?></p>

            <h6>Victim Information</h6>
            <p>Name: <?= $row['victim_name'] ?><br>Address: <?= $row['victim_address'] ?><br>Phone: <?= $row['victim_phone'] ?><br>Email: <?= $row['victim_email'] ?><br>Gender: <?= $row['victim_gender'] ?><br>DOB: <?= $row['victim_dob'] ?><br>
            <?= ($row['victim_pic'] !== 'Not Added') ? "<img src='../uploads/{$row['victim_pic']}' alt='Victim Photo' class='img-thumbnail' width='120'>" : "Victim Photo: Not Added" ?>
            </p>

            <h6>Suspect Information</h6>
            <p>Name: <?= $row['suspect_name'] ?><br>Address: <?= $row['suspect_address'] ?><br>Phone: <?= $row['suspect_phone'] ?><br>Gender: <?= $row['suspect_gender'] ?><br>DOB: <?= $row['suspect_dob'] ?><br>Known Offender: <?= $row['suspect_known_offender'] ?><br>
            <?= ($row['suspect_pic'] !== 'Not Added') ? "<img src='../uploads/{$row['suspect_pic']}' alt='Suspect Photo' class='img-thumbnail' width='120'>" : "Suspect Photo: Not Added" ?>
            </p>

        </div>
    </div>

<?php
} else {
    echo "<div class='alert alert-info'>No details found for this crime.</div>";
}

$stmt->close();
$conn->close();
?>

</div>
</body>
</html>
