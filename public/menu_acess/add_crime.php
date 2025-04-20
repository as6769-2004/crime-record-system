<?php
// Include your database connection file
include '../../includes/db_connect.php';

// Station data (static for now)
$station_name = "Your Station Name";
$station_code = "ST001";
$station_address = "123 Main St, Anytown";
$station_id = 1;

// Fetch last case number
$last_case_query = "SELECT case_number FROM CRIME ORDER BY crime_id DESC LIMIT 1";
$result_last_case = $conn->query($last_case_query);
$last_case = "CN0000";

if ($result_last_case && $result_last_case->num_rows > 0) {
    $row = $result_last_case->fetch_assoc();
    $last_case = $row['case_number'];
}

// Extract number, increment, and generate next case number
if (preg_match('/CN(\d+)/', $last_case, $matches)) {
    $number = intval($matches[1]) + 1;
    $next_case_number = 'CN' . str_pad($number, 4, '0', STR_PAD_LEFT);
} else {
    $next_case_number = $last_case;
}


// Handle form submission
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

    $stmt_insert = $conn->prepare("INSERT INTO CRIME (crime_type, crime_date, location, description, victim_id, suspect_id, officer_id, status, case_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssiiiss", $crime_type, $crime_date, $location, $description, $victim_id, $suspect_id, $officer_id, $status, $case_number);

    if ($stmt_insert->execute()) {
        $crime_id = $conn->insert_id;
        $message = "Crime record added successfully.";

        $folder_path = "../../assets/cases/crime_" . $crime_id;
        if (!file_exists($folder_path)) {
            mkdir($folder_path, 0755, true);
        }

        if (isset($_FILES["images"]) && !empty(array_filter($_FILES['images']['name']))) {
            $uploadedFiles = $_FILES["images"];
            $fileCount = count($uploadedFiles["name"]);

            for ($i = 0; $i < $fileCount; $i++) {
                if ($uploadedFiles["error"][$i] == UPLOAD_ERR_OK) {
                    $fileName = basename($uploadedFiles["name"][$i]);
                    $targetFile = $folder_path . "/" . $fileName;
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                    $allowedTypes = ["jpg", "jpeg", "png", "gif"];
                    if (in_array($imageFileType, $allowedTypes)) {
                        if (move_uploaded_file($uploadedFiles["tmp_name"][$i], $targetFile)) {
                            $message .= "<br>File " . htmlspecialchars($fileName) . " uploaded successfully.";
                        } else {
                            $message .= "<br>Error uploading file " . htmlspecialchars($fileName) . ".";
                        }
                    } else {
                        $message .= "<br>Invalid file type for " . htmlspecialchars($fileName) . ".";
                    }
                } else {
                    $message .= "<br>Error uploading file " . htmlspecialchars($uploadedFiles["name"][$i]) . ": " . $uploadedFiles["error"][$i];
                }
            }
        }
    } else {
        $message = "Error adding crime record: " . $stmt_insert->error;
    }

    $stmt_insert->close();
}

// Fetch officers
$stmt_officers = $conn->prepare("SELECT officer_id, name, badge_number FROM OFFICER WHERE station_id = ?");
$stmt_officers->bind_param("i", $station_id);
$stmt_officers->execute();
$result_officers = $stmt_officers->get_result();

$officers = [];
while ($row_officer = $result_officers->fetch_assoc()) {
    $officers[] = $row_officer;
}

// Fetch victims
$stmt_victims = $conn->prepare("SELECT victim_id, name FROM VICTIM");
$stmt_victims->execute();
$result_victims = $stmt_victims->get_result();

$victims = [];
while ($row_victim = $result_victims->fetch_assoc()) {
    $victims[] = $row_victim;
}

// Fetch suspects
$stmt_suspects = $conn->prepare("SELECT suspect_id, name FROM SUSPECT");
$stmt_suspects->execute();
$result_suspects = $stmt_suspects->get_result();

$suspects = [];
while ($row_suspect = $result_suspects->fetch_assoc()) {
    $suspects[] = $row_suspect;
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
    <div class="container">
        <div>
            <h2>Add Crime Record</h2>
            <?php if (isset($message)) {
                echo "<p>" . htmlspecialchars($message) . "</p>";
            } ?>
            <form method="post" enctype="multipart/form-data">
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
                    <option value="open">Active</option>
                    <option value="under investigation">Ongoing</option>
                    <option value="closed">Closed</option>
                </select><br><br>

                <label for="case_number">Case Number:</label><br>
                <input type="text" id="case_number" name="case_number" value="<?php echo $next_case_number; ?>" readonly required><br><br>

                <label for="images">Upload Images:</label><br>
                <input type="file" name="images[]" multiple><br><br>

                <input type="submit" value="Submit">

            </form>

        </div>

        <div class="image-preview-container" id="imagePreviewContainer"></div>

    </div>

    <script>
        document.querySelector('input[name="images[]"]').addEventListener('change', function() {
            const files = this.files;
            const previewContainer = document.getElementById('imagePreviewContainer');
            previewContainer.innerHTML = '';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();

                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.classList.add('image-preview');

                    const img = document.createElement('img');
                    img.src = e.target.result;

                    const filename = document.createElement('div');
                    filename.classList.add('image-filename');
                    filename.textContent = file.name;

                    preview.appendChild(img);
                    preview.appendChild(filename);
                    previewContainer.appendChild(preview);
                };

                reader.readAsDataURL(file);
            }
        });
    </script>
</body>

</html>