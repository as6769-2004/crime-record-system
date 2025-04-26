<?php
include '../../includes/db_connect.php';

$created_by = isset($_GET['officer_id']) ? intval($_GET['officer_id']) : 0;
$station_name = isset($_GET['station_name']) ? $_GET['station_name'] : 'Default Station';

// Fetch station_id from officer
$stmt = $conn->prepare("SELECT station_id FROM officer WHERE officer_id = ?");
$stmt->bind_param("i", $created_by);
$stmt->execute();
$stmt->bind_result($fetched_station_id);
if ($stmt->fetch()) {
    $station_id = $fetched_station_id;
}
$stmt->close();

// Get next case number
$last_case_query = "SELECT case_number FROM CRIME ORDER BY crime_id DESC LIMIT 1";
$result_last_case = $conn->query($last_case_query);
$last_case = "CN0000";

if ($result_last_case && $result_last_case->num_rows > 0) {
    $row = $result_last_case->fetch_assoc();
    $last_case = $row['case_number'];
}

if (preg_match('/CN(\d+)/', $last_case, $matches)) {
    $number = intval($matches[1]) + 1;
    $next_case_number = 'CN' . str_pad($number, 4, '0', STR_PAD_LEFT);
} else {
    $next_case_number = 'CN0001';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $crime_type = $_POST["crime_type"];
    $crime_date = $_POST["crime_date"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $officer_id = $_POST["officer_id"];
    $status = $_POST["status"];
    $case_number = $_POST["case_number"];

    // Selected Victims
    $selected_victims = isset($_POST['selected_victims']) ? explode(',', $_POST['selected_victims']) : [];

    // Selected Suspects
    $selected_suspects = isset($_POST['selected_suspects']) ? explode(',', $_POST['selected_suspects']) : [];

    // Selected Witnesses
    $selected_witnesses = isset($_POST['selected_witnesses']) ? explode(',', $_POST['selected_witnesses']) : [];


    // Insert into the CRIME table
    $stmt_insert = $conn->prepare("INSERT INTO CRIME (crime_type, crime_date, location, description, officer_id, status, case_number, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt_insert->bind_param("ssssisss", $crime_type, $crime_date, $location, $description, $officer_id, $status, $case_number, $created_by);

    if ($stmt_insert->execute()) {
        $crime_id = $conn->insert_id;
        $message = "Crime record added successfully.";

        // Add selected victims to the crime_victims table
        if (!empty($selected_victims)) {
            $stmt_victim = $conn->prepare("INSERT INTO crime_victims (crime_id, victim_id) VALUES (?, ?)");
            foreach ($selected_victims as $victim_id) {
                $victim_id = intval($victim_id);
                $stmt_victim->bind_param("ii", $crime_id, $victim_id);
                if (!$stmt_victim->execute()) {
                    $message .= "<br>Failed to add victim ID " . htmlspecialchars($victim_id);
                }
            }
            $stmt_victim->close();
        }


        // Add selected witnesses to the crime_witnesses table
        if (!empty($selected_witnesses)) {
            $stmt_witness = $conn->prepare("INSERT INTO crime_witnesses (crime_id, witness_id) VALUES (?, ?)");
            foreach ($selected_witnesses as $witness_id) {
                $witness_id = intval($witness_id);
                $stmt_witness->bind_param("ii", $crime_id, $witness_id);
                if (!$stmt_witness->execute()) {
                    $message .= "<br>Failed to add witness ID " . htmlspecialchars($witness_id);
                }
            }
            $stmt_witness->close();
        }


        // Add selected suspect to the crime_suspects table (if suspect ID exists)
        if (!empty($selected_suspects)) {
            $stmt_suspect = $conn->prepare("INSERT INTO crime_suspects (crime_id, suspect_id) VALUES (?, ?)");
            foreach ($selected_suspects as $suspect_id) {
                $suspect_id = intval($suspect_id);
                $stmt_suspect->bind_param("ii", $crime_id, $suspect_id);
                if (!$stmt_suspect->execute()) {
                    $message .= "<br>Failed to add suspect ID " . htmlspecialchars($suspect_id);
                }
            }
            $stmt_suspect->close();
        }


        // Handle file uploads (if any)
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


// Fetch dropdowns
$officers = $victims = $suspects = $witnesses = [];

// Fetch officers
$stmt = $conn->prepare("SELECT officer_id, name, badge_number FROM OFFICER WHERE station_id = ?");
$stmt->bind_param("i", $station_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc())
    $officers[] = $row;
$stmt->close();

// Fetch victims
$stmt = $conn->prepare("SELECT victim_id, name FROM VICTIM");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc())
    $victims[] = $row;
$stmt->close();

// Fetch suspects
$stmt = $conn->prepare("SELECT suspect_id, name FROM SUSPECT");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc())
    $suspects[] = $row;
$stmt->close();

// Fetch witnesses
$stmt = $conn->prepare("SELECT witness_id, name FROM WITNESS");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc())
    $witnesses[] = $row;
$stmt->close();

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
        <h2>Add Crime Record</h2>

        <!-- Message display after submission -->
        <?php if (isset($message)) {
            echo "<p>" . htmlspecialchars($message) . "</p>";
        } ?>

        <!-- Form to add crime record -->
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="created_by" value="<?php echo htmlspecialchars($created_by); ?>">

            <!-- Crime Type -->
            <label for="crime_type">Crime Type:</label><br>
            <input type="text" id="crime_type" name="crime_type" required><br><br>

            <!-- Crime Date -->
            <label for="crime_date">Crime Date:</label><br>
            <input type="datetime-local" id="crime_date" name="crime_date" required><br><br>

            <!-- Location -->
            <label for="location">Location:</label><br>
            <input type="text" id="location" name="location" required><br><br>

            <!-- Description -->
            <label for="description">Description:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <!-- Victim Selection -->
            <label for="victimDropdown">Select Victim:</label><br>
            <select id="victimDropdown">
                <option value="">Select Victim</option>
                <?php foreach ($victims as $victim) { ?>
                    <option value="<?php echo htmlspecialchars($victim['victim_id']); ?>">
                        <?php echo htmlspecialchars($victim['name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="button" onclick="addVictim()">Add Victim</button><br><br>

            <!-- List of Selected Victims -->
            <h4>Selected Victims:</h4>
            <ul id="selectedVictimsList"></ul>

            <!-- Hidden input to store selected victim IDs -->
            <input type="hidden" name="selected_victims" id="selected_victims">

            <!-- Suspect Selection -->
            <label for="suspectDropdown">Select Suspect:</label><br>
            <select id="suspectDropdown">
                <option value="">Select Suspect</option>
                <?php foreach ($suspects as $suspect) { ?>
                    <option value="<?php echo htmlspecialchars($suspect['suspect_id']); ?>">
                        <?php echo htmlspecialchars($suspect['name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="button" onclick="addSuspect()">Add Suspect</button><br><br>

            <!-- List of Selected Suspects -->
            <h4>Selected Suspects:</h4>
            <ul id="selectedSuspectsList"></ul>

            <!-- Hidden input to store selected suspect IDs -->
            <input type="hidden" name="selected_suspects" id="selected_suspects">

            <!-- Witness Selection -->
            <label for="witnessDropdown">Select Witnesses:</label><br>
            <select id="witnessDropdown">
                <?php foreach ($witnesses as $witness) { ?>
                    <option value="<?php echo htmlspecialchars($witness['witness_id']); ?>">
                        <?php echo htmlspecialchars($witness['name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="button" onclick="addWitness()">Add Witnesses</button><br><br>

            <!-- List of Selected Witnesses -->
            <h4>Selected Witnesses:</h4>
            <ul id="selectedWitnessesList"></ul>

            <!-- Hidden input to store selected witness IDs -->
            <input type="hidden" name="selected_witnesses" id="selected_witnesses">

            <!-- Officer In Charge -->
            <label for="officer_id">Officer In Charge:</label><br>
            <select id="officer_id" name="officer_id" required>
                <option value="">Select Officer</option>
                <?php foreach ($officers as $officer) { ?>
                    <option value="<?php echo htmlspecialchars($officer['officer_id']); ?>">
                        <?php echo htmlspecialchars($officer['name']); ?>
                    </option>
                <?php } ?>
            </select><br><br>

            <!-- Crime Status -->
            <label for="status">Status:</label><br>
            <select name="status" id="status" required>
                <option value="open">Open</option>
                <option value="under investigation">Under Investigation</option>
                <option value="closed">Closed</option>
            </select><br><br>



            <!-- Case Number -->
            <label for="case_number">Case Number:</label><br>
            <input type="text" id="case_number" name="case_number"
                value="<?php echo htmlspecialchars($next_case_number); ?>" required><br><br>

            <!-- Attach Images -->
            <label for="images">Attach Images (optional):</label><br>
            <input type="file" name="images[]" multiple><br><br>

            <!-- Submit Button -->
            <button type="submit">Submit</button>
        </form>
    </div>

    <!-- JavaScript for Dynamic Management of Victim, Suspect, and Witness -->
    <script>
        // Global arrays to store selected IDs
        let selectedVictimIds = [];
        let selectedSuspectIds = [];
        let selectedWitnessIds = [];

        // Function to add a victim
        function addVictim() {
            const dropdown = document.getElementById('victimDropdown');
            const id = dropdown.value;
            const name = dropdown.options[dropdown.selectedIndex].text;

            if (id && !selectedVictimIds.includes(id)) {
                selectedVictimIds.push(id);

                const ul = document.getElementById('selectedVictimsList');
                const li = document.createElement('li');
                li.textContent = name;

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.onclick = () => {
                    selectedVictimIds = selectedVictimIds.filter(victimId => victimId !== id);
                    ul.removeChild(li);
                    updateVictimList();
                };

                li.appendChild(removeButton);
                ul.appendChild(li);
                updateVictimList();
            }
        }

        function updateVictimList() {
            document.getElementById('selected_victims').value = selectedVictimIds.join(',');
        }

        // Function to add a suspect
        function addSuspect() {
            const dropdown = document.getElementById('suspectDropdown');
            const id = dropdown.value;
            const name = dropdown.options[dropdown.selectedIndex].text;

            if (id && !selectedSuspectIds.includes(id)) {
                selectedSuspectIds.push(id);

                const ul = document.getElementById('selectedSuspectsList');
                const li = document.createElement('li');
                li.textContent = name;

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.onclick = () => {
                    selectedSuspectIds = selectedSuspectIds.filter(suspectId => suspectId !== id);
                    ul.removeChild(li);
                    updateSuspectList();
                };

                li.appendChild(removeButton);
                ul.appendChild(li);
                updateSuspectList();
            }
        }

        function updateSuspectList() {
            document.getElementById('selected_suspects').value = selectedSuspectIds.join(',');
        }

        // Function to add a witness
        function addWitness() {
            const dropdown = document.getElementById('witnessDropdown');
            const id = dropdown.value;
            const name = dropdown.options[dropdown.selectedIndex].text;

            if (id && !selectedWitnessIds.includes(id)) {
                selectedWitnessIds.push(id);

                const ul = document.getElementById('selectedWitnessesList');
                const li = document.createElement('li');
                li.textContent = name;

                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.onclick = () => {
                    selectedWitnessIds = selectedWitnessIds.filter(witnessId => witnessId !== id);
                    ul.removeChild(li);
                    updateWitnessList();
                };

                li.appendChild(removeButton);
                ul.appendChild(li);
                updateWitnessList();
            }
        }

        function updateWitnessList() {
            document.getElementById('selected_witnesses').value = selectedWitnessIds.join(',');
        }
    </script>

</body>

</html>