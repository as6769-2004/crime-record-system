<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);  // Ensuring crime_id is a valid integer

// Fetch crime details
$query = "SELECT crime_id, crime_type, case_number FROM crime WHERE crime_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $crime_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='alert alert-danger'>Crime not found.</div>");
}
$crime = $result->fetch_assoc();

// Fetch suspects associated with this crime
$suspectQuery = "SELECT s.* FROM suspect s INNER JOIN crime_suspects cs ON s.suspect_id = cs.suspect_id WHERE cs.crime_id = ?";
$suspectStmt = $conn->prepare($suspectQuery);
$suspectStmt->bind_param("i", $crime_id);
$suspectStmt->execute();
$suspectsResult = $suspectStmt->get_result();

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle suspect drop
    if (isset($_POST['drop_suspect'])) {
        $suspect_id = intval($_POST['suspect_id']);

        // Remove suspect from crime
        $dropSuspectQuery = "DELETE FROM crime_suspects WHERE crime_id = ? AND suspect_id = ?";
        $dropStmt = $conn->prepare($dropSuspectQuery);
        $dropStmt->bind_param("ii", $crime_id, $suspect_id);
        if ($dropStmt->execute()) {
            echo "<div class='alert alert-success'>Suspect dropped successfully from this crime.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error dropping suspect from crime.</div>";
        }
        $dropStmt->close();
    }

    // Handle adding new suspect
    if (isset($_POST['add_suspect']) && isset($_POST['new_suspect_id'])) {
        $new_suspect_id = intval($_POST['new_suspect_id']);

        // Check if suspect already associated
        $checkQuery = "SELECT * FROM crime_suspects WHERE crime_id = ? AND suspect_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $crime_id, $new_suspect_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            // Add suspect
            $insertSuspectQuery = "INSERT INTO crime_suspects (crime_id, suspect_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertSuspectQuery);
            $insertStmt->bind_param("ii", $crime_id, $new_suspect_id);
            if ($insertStmt->execute()) {
                $successMessage = 'Suspect added successfully to this crime.';
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            } else {
                echo "<div class='alert alert-danger'>Error adding suspect to crime.</div>";
            }
            $insertStmt->close();
        } else {
            echo "<div class='alert alert-warning'>Suspect $new_suspect_id is already associated with this crime.</div>";
        }
        $checkStmt->close();
    }
}

// Fetch all suspects
$allSuspectsQuery = "SELECT * FROM suspect";
$allSuspectsStmt = $conn->prepare($allSuspectsQuery);
$allSuspectsStmt->execute();
$allSuspectsResult = $allSuspectsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Crime - Suspects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">

        <h2 class="mb-4 text-center">Manage Suspects for Case #<?= htmlspecialchars($crime['case_number']) ?>
            (<?= htmlspecialchars($crime['crime_type']) ?>)</h2>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- List of Current Suspects -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-danger">
                <h5>Current Suspects</h5>
            </div>
            <div class="card-body">
                <?php if ($suspectsResult->num_rows > 0): ?>
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Suspect ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($suspect = $suspectsResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $suspect['suspect_id'] ?></td>
                                    <td><?= htmlspecialchars($suspect['name']) ?></td>
                                    <td><?= htmlspecialchars($suspect['phone']) ?></td>
                                    <td><?= htmlspecialchars($suspect['address']) ?></td>
                                    <td>
                                        <form action="update_suspect.php?crime_id=<?= $crime_id ?>" method="post"
                                            onsubmit="return confirm('Are you sure you want to drop this suspect from the crime?');"
                                            style="display:inline;">
                                            <input type="hidden" name="suspect_id" value="<?= $suspect['suspect_id'] ?>">
                                            <button type="submit" name="drop_suspect" class="btn btn-danger btn-sm">Drop
                                                Suspect</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No suspects associated with this crime yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Suspect Form -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-warning">
                <h5>Add Suspect to Crime</h5>
            </div>
            <div class="card-body">
                <form action="update_suspect.php?crime_id=<?= $crime_id ?>" method="post">
                    <div class="mb-3">
                        <label for="new_suspect_id" class="form-label">Select Suspect to Add</label>
                        <select class="form-select" id="new_suspect_id" name="new_suspect_id" required>
                            <option value="">Select Suspect</option>
                            <?php
                            // Fetch IDs of already added suspects
                            $alreadyAddedSuspects = [];
                            $suspectStmt->execute();
                            $suspectsResult = $suspectStmt->get_result();
                            while ($s = $suspectsResult->fetch_assoc()) {
                                $alreadyAddedSuspects[] = $s['suspect_id'];
                            }

                            // Reset allSuspectsResult pointer
                            $allSuspectsStmt->execute();
                            $allSuspectsResult = $allSuspectsStmt->get_result();

                            while ($suspect = $allSuspectsResult->fetch_assoc()):
                                if (!in_array($suspect['suspect_id'], $alreadyAddedSuspects)):
                                    ?>
                                    <option value="<?= $suspect['suspect_id'] ?>"><?= htmlspecialchars($suspect['name']) ?>
                                    </option>
                                    <?php
                                endif;
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="add_suspect" class="btn btn-warning"
                        onclick="window.location.reload();">Add Suspect</button>
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
$suspectStmt->close();
$allSuspectsStmt->close();
$conn->close();
?>