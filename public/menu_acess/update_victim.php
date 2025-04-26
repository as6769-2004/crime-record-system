<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);  // Ensuring crime_id is a valid integer

// Fetch crime details
$query = "SELECT crime_id, crime_type, case_number FROM crime WHERE crime_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $crime_id);  // Binding the crime_id as an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='alert alert-danger'>Crime not found.</div>");
}
$crime = $result->fetch_assoc();

// Fetch victims associated with this crime
$victimQuery = "SELECT v.* FROM victim v INNER JOIN crime_victims cv ON v.victim_id = cv.victim_id WHERE cv.crime_id = ?";
$victimStmt = $conn->prepare($victimQuery);
$victimStmt->bind_param("i", $crime_id);  // Binding the crime_id as an integer for victims
$victimStmt->execute();
$victimsResult = $victimStmt->get_result();

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle victim drop
    if (isset($_POST['drop_victim'])) {
        $victim_id = intval($_POST['victim_id']);

        // Remove victim from crime
        $dropVictimQuery = "DELETE FROM crime_victims WHERE crime_id = ? AND victim_id = ?";
        $dropStmt = $conn->prepare($dropVictimQuery);
        $dropStmt->bind_param("ii", $crime_id, $victim_id);
        if ($dropStmt->execute()) {
            echo "<div class='alert alert-success'>Victim dropped successfully from this crime.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error dropping victim from crime.</div>";
        }
        $dropStmt->close();
    }

    // Handle adding new victim to crime (if a victim is selected)
    if (isset($_POST['add_victim']) && isset($_POST['new_victim_id'])) {
        $new_victim_id = intval($_POST['new_victim_id']);

        // Check if the victim is already associated with the crime
        $checkQuery = "SELECT * FROM crime_victims WHERE crime_id = ? AND victim_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $crime_id, $new_victim_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Victim is not associated, so add them to the crime
            $insertVictimQuery = "INSERT INTO crime_victims (crime_id, victim_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertVictimQuery);
            $insertStmt->bind_param("ii", $crime_id, $new_victim_id);
            if ($insertStmt->execute()) {
                // Set success message and trigger page refresh
                $successMessage = 'Victim added successfully to this crime.';
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            } else {
                echo "<div class='alert alert-danger'>Error adding victim to crime.</div>";
            }
            $insertStmt->close();
        } else {
            echo "<div class='alert alert-warning'>Victim $new_victim_id is already associated with this crime.</div>";
        }
        $checkStmt->close();
    }
}

// Fetch all victims for adding to the crime
$allVictimsQuery = "SELECT * FROM victim";
$allVictimsStmt = $conn->prepare($allVictimsQuery);
$allVictimsStmt->execute();
$allVictimsResult = $allVictimsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Crime - Victims</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>


<body class="bg-dark text-light">
    <div class="container py-4">

        <h2 class="mb-4 text-center">Manage Victims for Case #<?= htmlspecialchars($crime['case_number']) ?>
            (<?= htmlspecialchars($crime['crime_type']) ?>)</h2>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- List of Current Victims -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-info">
                <h5>Current Victims</h5>
            </div>
            <div class="card-body">
                <?php if ($victimsResult->num_rows > 0): ?>
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Victim ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($victim = $victimsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $victim['victim_id'] ?></td>
                                    <td><?= htmlspecialchars($victim['name']) ?></td>
                                    <td><?= htmlspecialchars($victim['phone']) ?></td>
                                    <td><?= htmlspecialchars($victim['email']) ?></td>
                                    <td>
                                        <form action="update_victim.php?crime_id=<?= $crime_id ?>" method="post"
                                            onsubmit="return confirm('Are you sure you want to drop this victim from the crime?');"
                                            style="display:inline;">
                                            <input type="hidden" name="victim_id" value="<?= $victim['victim_id'] ?>">
                                            <button type="submit" name="drop_victim" class="btn btn-danger btn-sm">Drop
                                                Victim</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No victims associated with this crime yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Victim Form -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-warning">
                <h5>Add Victim to Crime</h5>
            </div>
            <div class="card-body">
                <form action="update_victim.php?crime_id=<?= $crime_id ?>" method="post">
                    <div class="mb-3">
                        <label for="new_victim_id" class="form-label">Select Victim to Add</label>
                        <select class="form-select" id="new_victim_id" name="new_victim_id" required>
                            <option value="">Select Victim</option>
                            <?php
                            // Fetch IDs of already added victims
                            $alreadyAddedVictims = [];
                            $victimStmt->execute();
                            $victimsResult = $victimStmt->get_result();
                            while ($v = $victimsResult->fetch_assoc()) {
                                $alreadyAddedVictims[] = $v['victim_id'];
                            }

                            // Reset allVictimsResult pointer
                            $allVictimsStmt->execute();
                            $allVictimsResult = $allVictimsStmt->get_result();

                            while ($victim = $allVictimsResult->fetch_assoc()):
                                if (!in_array($victim['victim_id'], $alreadyAddedVictims)):
                                    ?>
                                    <option value="<?= $victim['victim_id'] ?>"><?= htmlspecialchars($victim['name']) ?>
                                    </option>
                                    <?php
                                endif;
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="add_victim" class="btn btn-warning"
                        onclick="window.location.reload();">Add Victim</button>
                </form>
            </div>
        </div>

        <!-- Manual Refresh Button -->
        <div class="text-center">
            <button class="btn btn-primary" onclick="window.location.reload();">Refresh Page</button>
        </div>

    </div>
</body>

</html>

<?php
$stmt->close();
$victimStmt->close();
$allVictimsStmt->close();
$conn->close();
?>