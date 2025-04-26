<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);

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

// Fetch witnesses associated with this crime
$witnessQuery = "SELECT w.* FROM witness w INNER JOIN crime_witnesses cw ON w.witness_id = cw.witness_id WHERE cw.crime_id = ?";
$witnessStmt = $conn->prepare($witnessQuery);
$witnessStmt->bind_param("i", $crime_id);
$witnessStmt->execute();
$witnessResult = $witnessStmt->get_result();

$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle witness drop
    if (isset($_POST['drop_witness'])) {
        $witness_id = intval($_POST['witness_id']);

        $dropWitnessQuery = "DELETE FROM crime_witnesses WHERE crime_id = ? AND witness_id = ?";
        $dropStmt = $conn->prepare($dropWitnessQuery);
        $dropStmt->bind_param("ii", $crime_id, $witness_id);
        if ($dropStmt->execute()) {
            echo "<div class='alert alert-success'>Witness dropped successfully from this crime.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error dropping witness from crime.</div>";
        }
        $dropStmt->close();
    }

    // Handle adding new witness
    if (isset($_POST['add_witness']) && isset($_POST['new_witness_id'])) {
        $new_witness_id = intval($_POST['new_witness_id']);

        $checkQuery = "SELECT * FROM crime_witnesses WHERE crime_id = ? AND witness_id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ii", $crime_id, $new_witness_id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows === 0) {
            $insertWitnessQuery = "INSERT INTO crime_witnesses (crime_id, witness_id) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertWitnessQuery);
            $insertStmt->bind_param("ii", $crime_id, $new_witness_id);
            if ($insertStmt->execute()) {
                $successMessage = 'Witness added successfully to this crime.';
                echo "<script>setTimeout(function(){ location.reload(); }, 2000);</script>";
            } else {
                echo "<div class='alert alert-danger'>Error adding witness to crime.</div>";
            }
            $insertStmt->close();
        } else {
            echo "<div class='alert alert-warning'>Witness $new_witness_id is already associated with this crime.</div>";
        }
        $checkStmt->close();
    }
}

// Fetch all witnesses
$allWitnessQuery = "SELECT * FROM witness";
$allWitnessStmt = $conn->prepare($allWitnessQuery);
$allWitnessStmt->execute();
$allWitnessResult = $allWitnessStmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Crime - Witnesses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-dark text-light">
    <div class="container py-4">

        <h2 class="mb-4 text-center">Manage Witnesses for Case #<?= htmlspecialchars($crime['case_number']) ?>
            (<?= htmlspecialchars($crime['crime_type']) ?>)</h2>

        <?php if ($successMessage): ?>
            <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- Current Witnesses -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-info">
                <h5>Current Witnesses</h5>
            </div>
            <div class="card-body">
                <?php if ($witnessResult->num_rows > 0): ?>
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>Witness ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($witness = $witnessResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $witness['witness_id'] ?></td>
                                    <td><?= htmlspecialchars($witness['name']) ?></td>
                                    <td><?= htmlspecialchars($witness['phone']) ?></td>
                                    <td><?= htmlspecialchars($witness['email']) ?></td>
                                    <td>
                                        <form action="update_witness.php?crime_id=<?= $crime_id ?>" method="post"
                                            onsubmit="return confirm('Are you sure you want to drop this witness?');"
                                            style="display:inline;">
                                            <input type="hidden" name="witness_id" value="<?= $witness['witness_id'] ?>">
                                            <button type="submit" name="drop_witness" class="btn btn-danger btn-sm">Drop
                                                Witness</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">No witnesses associated with this crime yet.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Witness -->
        <div class="card mb-4 shadow bg-secondary text-light">
            <div class="card-header bg-warning">
                <h5>Add Witness to Crime</h5>
            </div>
            <div class="card-body">
                <form action="update_witness.php?crime_id=<?= $crime_id ?>" method="post">
                    <div class="mb-3">
                        <label for="new_witness_id" class="form-label">Select Witness to Add</label>
                        <select class="form-select" id="new_witness_id" name="new_witness_id" required>
                            <option value="">Select Witness</option>
                            <?php
                            // Fetch already added witnesses
                            $alreadyAddedWitnesses = [];
                            $witnessStmt->execute();
                            $witnessResult = $witnessStmt->get_result();
                            while ($w = $witnessResult->fetch_assoc()) {
                                $alreadyAddedWitnesses[] = $w['witness_id'];
                            }

                            $allWitnessStmt->execute();
                            $allWitnessResult = $allWitnessStmt->get_result();

                            while ($witness = $allWitnessResult->fetch_assoc()):
                                if (!in_array($witness['witness_id'], $alreadyAddedWitnesses)):
                                    ?>
                                    <option value="<?= $witness['witness_id'] ?>"><?= htmlspecialchars($witness['name']) ?>
                                    </option>
                                    <?php
                                endif;
                            endwhile;
                            ?>
                        </select>
                    </div>

                    <button type="submit" name="add_witness" class="btn btn-warning">Add Witness</button>
                </form>
            </div>
        </div>

        <!-- Manual Refresh -->
        <div class="text-center mb-4">
            <button class="btn btn-primary" onclick="window.location.reload();">Refresh Page</button>
        </div>

        <!-- Take Action Button -->
        <div class="text-center mt-4">
            <a href="update_witness.php?crime_id=<?= $crime_id ?>">
                <button class="btn btn-success btn-lg">Take Action on Witnesses</button>
            </a>
        </div>

    </div>
</body>

</html>

<?php
$stmt->close();
$witnessStmt->close();
$allWitnessStmt->close();
$conn->close();
?>