<?php
// Include your database connection file
include '../../includes/db_connect.php';

// Get station details (replace with your logic to fetch station data)
$station_name = "Your Station Name";
$station_code = "ST001";
$station_address = "123 Main St, Anytown";
$station_id = 1;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $crime_type = $_POST["crime_type"];
    $crime_date = $_POST["crime_date"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $victim_id = $_POST["victim_id"];
    $suspect_id = $_POST["suspect_id"];
    $officer_id = $_POST["officer_id"];
    $status = $_POST["status"];
    $case_number = $_POST["case_number"];

    // Insert crime record
    $stmt_insert = $conn->prepare("INSERT INTO CRIME (crime_type, crime_date, location, description, victim_id, suspect_id, officer_id, status, case_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssiiiis", $crime_type, $crime_date, $location, $description, $victim_id, $suspect_id, $officer_id, $status, $case_number);

    if ($stmt_insert->execute()) {
        $message = "Crime record added successfully.";
    } else {
        $message = "Error adding crime record: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

// Fetch officers for dropdown
$stmt_officers = $conn->prepare("SELECT officer_id, name, badge_number FROM OFFICER WHERE station_id = ?");
$stmt_officers->bind_param("i", $station_id);
$stmt_officers->execute();
$result_officers = $stmt_officers->get_result();

$officers = [];
if ($result_officers->num_rows > 0) {
    while ($row_officer = $result_officers->fetch_assoc()) {
        $officers[] = $row_officer;
    }
}

// Fetch victims for dropdown
$stmt_victims = $conn->prepare("SELECT victim_id, name FROM VICTIM");
$stmt_victims->execute();
$result_victims = $stmt_victims->get_result();

$victims = [];
if ($result_victims->num_rows > 0) {
    while ($row_victim = $result_victims->fetch_assoc()) {
        $victims[] = $row_victim;
    }
}

// Fetch suspects for dropdown
$stmt_suspects = $conn->prepare("SELECT suspect_id, name FROM SUSPECT");
$stmt_suspects->execute();
$result_suspects = $stmt_suspects->get_result();

$suspects = [];
if ($result_suspects->num_rows > 0) {
    while ($row_suspect = $result_suspects->fetch_assoc()) {
        $suspects[] = $row_suspect;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($station_name); ?> - Add Crime Record</title>
    <link rel="stylesheet" href="../../assets/css/add_crime.css">
</head>

<body>

    <div class="inner-right-column">
        <div>
            <h2>Add Crime Record</h2>
            <?php if (isset($message)) { echo "<p>" . htmlspecialchars($message) . "</p>"; } ?>
            <form method="post">
                <label for="crime_type">Crime Type:</label><br>
                <input type="text" id="crime_type" name="crime_type" required><br><br>

                <label for="crime_date">Crime Date:</label><br>
                <input type="datetime-local" id="crime_date" name="crime_date" required><br><br>

                <label for="location">Location:</label><br>
                <input type="text" id="location" name="location" required><br><br>

                <label for="description">Description:</label><br>
                <textarea id="description" name="description" required></textarea><br><br>

                <label for="victim_id">Victim:</label><br>
                <select id="victim_id" name="victim_id">
                    <option value="">Select Victim</option>
                    <?php foreach ($victims as $victim) { ?>
                        <option value="<?php echo htmlspecialchars($victim['victim_id']); ?>"><?php echo htmlspecialchars($victim['name']); ?></option>
                    <?php } ?>
                </select><br><br>

                <label for="suspect_id">Suspect:</label><br>
                <select id="suspect_id" name="suspect_id">
                    <option value="">Select Suspect</option>
                    <?php foreach ($suspects as $suspect) { ?>
                        <option value="<?php echo htmlspecialchars($suspect['suspect_id']); ?>"><?php echo htmlspecialchars($suspect['name']); ?></option>
                    <?php } ?>
                </select><br><br>

                <label for="officer_id">Officer:</label><br>
                <select id="officer_id" name="officer_id" required>
                    <option value="">Select Officer</option>
                    <?php foreach ($officers as $officer) { ?>
                        <option value="<?php echo htmlspecialchars($officer['officer_id']); ?>"><?php echo htmlspecialchars($officer['name']); ?> (Badge: <?php echo htmlspecialchars($officer['badge_number']); ?>)</option>
                    <?php } ?>
                </select><br><br>

                <label for="status">Status:</label><br>
                <select id="status" name="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select><br><br>

                <label for="case_number">Case Number:</label><br>
                <input type="text" id="case_number" name="case_number" required><br><br>

                <input type="submit" value="Submit">
            </form>
        </div>
    </div>

</body>

</html>