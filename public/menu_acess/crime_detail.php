<?php
// Include the database connection file
include '../../includes/db_connect.php';

// Retrieve the crime_id from the URL parameter (if provided)
$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);

// Query to get crime details
$query = "SELECT * FROM crime WHERE crime_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $crime_id);
$stmt->execute();
$crime_result = $stmt->get_result();
$crime = $crime_result->fetch_assoc();

// Query to get associated evidence for this crime
$evidence_query = "SELECT * FROM evidence WHERE crime_id = ?";
$evidence_stmt = $conn->prepare($evidence_query);
$evidence_stmt->bind_param("i", $crime_id);
$evidence_stmt->execute();
$evidence_result = $evidence_stmt->get_result();

// Query to get associated case logs for this crime
$logs_query = "SELECT * FROM case_logs WHERE crime_id = ?";
$logs_stmt = $conn->prepare($logs_query);
$logs_stmt->bind_param("i", $crime_id);
$logs_stmt->execute();
$logs_result = $logs_stmt->get_result();

// Query to get victims related to this crime
$victim_query = "
    SELECT v.victim_id, v.name, v.address, v.phone, v.email, v.date_of_birth, v.gender, v.victim_pic
    FROM crime_victims cv
    JOIN victim v ON cv.victim_id = v.victim_id
    WHERE cv.crime_id = ?";
$victim_stmt = $conn->prepare($victim_query);
$victim_stmt->bind_param("i", $crime_id);
$victim_stmt->execute();
$victim_result = $victim_stmt->get_result();

// Query to get suspects related to this crime
$suspect_query = "
    SELECT s.suspect_id, s.name, s.address, s.phone, s.date_of_birth, s.gender, s.suspect_pic, s.known_offender
    FROM crime_suspects cs
    JOIN suspect s ON cs.suspect_id = s.suspect_id
    WHERE cs.crime_id = ?";
$suspect_stmt = $conn->prepare($suspect_query);
$suspect_stmt->bind_param("i", $crime_id);
$suspect_stmt->execute();
$suspect_result = $suspect_stmt->get_result();

// Query to get witnesses related to this crime
$witness_query = "
    SELECT w.witness_id, w.name, w.address, w.phone, w.email, w.date_of_birth, w.gender, w.witness_pic
    FROM crime_witnesses cw
    JOIN witness w ON cw.witness_id = w.witness_id
    WHERE cw.crime_id = ?";
$witness_stmt = $conn->prepare($witness_query);
$witness_stmt->bind_param("i", $crime_id);
$witness_stmt->execute();
$witness_result = $witness_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Details</title>
    <style>
        /* Dark Theme Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        h1,
        h2 {
            color: #f1f1f1;
        }

        h2 {
            margin-top: 20px;
            text-decoration: underline;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            padding: 20px;
        }

        .tile {
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .tile:hover {
            transform: scale(1.05);
        }

        .tile img {
            max-width: 100%;
            border-radius: 5px;
        }

        .tile strong {
            color: #f1f1f1;
        }

        a button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        a button:hover {
            background-color: #45a049;
        }

        .tile p {
            font-size: 14px;
            margin: 5px 0;
        }

        .tile ul {
            padding-left: 20px;
            list-style-type: none;
        }

        .tile li {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="tile">
            <h2>Crime Information</h2>
            <p><strong>Crime Type:</strong> <?php echo $crime['crime_type']; ?></p>
            <p><strong>Date:</strong> <?php echo $crime['crime_date']; ?></p>
            <p><strong>Location:</strong> <?php echo $crime['location']; ?></p>
            <p><strong>Description:</strong> <?php echo $crime['description']; ?></p>
            <p><strong>Status:</strong> <?php echo $crime['status']; ?></p>
        </div>

        <div class="tile">
            <h2>Victims</h2>
            <?php if ($victim_result->num_rows > 0): ?>
                <ul>
                    <?php while ($victim = $victim_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $victim['name']; ?><br>
                            <strong>Address:</strong> <?php echo $victim['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $victim['phone']; ?><br>
                            <strong>Email:</strong> <?php echo $victim['email']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $victim['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($victim['gender']); ?><br>
                            <?php if (!empty($victim['victim_pic'])): ?>
                                <img src="<?php echo $victim['victim_pic']; ?>" alt="Victim Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No victims found for this crime.</p>
            <?php endif; ?>
        </div>

        <div class="tile">
            <h2>Suspects</h2>
            <?php if ($suspect_result->num_rows > 0): ?>
                <ul>
                    <?php while ($suspect = $suspect_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $suspect['name']; ?><br>
                            <strong>Address:</strong> <?php echo $suspect['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $suspect['phone']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $suspect['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($suspect['gender']); ?><br>
                            <strong>Known Offender:</strong> <?php echo $suspect['known_offender'] ? 'Yes' : 'No'; ?><br>
                            <?php if (!empty($suspect['suspect_pic'])): ?>
                                <img src="<?php echo $suspect['suspect_pic']; ?>" alt="Suspect Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No suspects found for this crime.</p>
            <?php endif; ?>
        </div>

        <div class="tile">
            <h2>Witnesses</h2>
            <?php if ($witness_result->num_rows > 0): ?>
                <ul>
                    <?php while ($witness = $witness_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $witness['name']; ?><br>
                            <strong>Address:</strong> <?php echo $witness['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $witness['phone']; ?><br>
                            <strong>Email:</strong> <?php echo $witness['email']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $witness['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($witness['gender']); ?><br>
                            <?php if (!empty($witness['witness_pic'])): ?>
                                <img src="<?php echo $witness['witness_pic']; ?>" alt="Witness Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No witnesses found for this crime.</p>
            <?php endif; ?>
        </div>

        <div class="tile">
            <h2>Evidence</h2>
            <?php if ($evidence_result->num_rows > 0): ?>
                <ul>
                    <?php while ($evidence = $evidence_result->fetch_assoc()): ?>
                        <li>
                            <strong>Description:</strong> <?php echo $evidence['description']; ?><br>
                            <strong>Location Found:</strong> <?php echo $evidence['location_found']; ?><br>
                            <strong>File:</strong> <a href="<?php echo $evidence['file_url']; ?>" download>Download File</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No evidence found for this crime.</p>
                <!-- Button to refer a link with crime_id -->
                <button class="btn btn-outline-info menu-button"
                    onclick="window.location.href='evidence.php?crime_id=<?php echo $crime_id; ?>'">Add Evidence</button>
            <?php endif; ?>

        </div>

        <!-- <div class="tile">
            <h2>Case Logs</h2>
            <?php if ($logs_result->num_rows > 0): ?>
                <ul>
                    <?php while ($log = $logs_result->fetch_assoc()): ?>
                        <li>
                            <strong>Log Date:</strong> <?php echo $log['log_date']; ?><br>
                            <strong>Entry:</strong> <?php echo $log['log_entry']; ?><br>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No case logs available for this crime.</p>
            <?php endif; ?>
        </div> -->
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="crime_update_pages.php?crime_id=<?= $crime_id ?>">
            <button>Take Action</button>
        </a>
    </div>

</body>

</html>

<?php
// Close the prepared statements
$stmt->close();
$evidence_stmt->close();
$logs_stmt->close();
$victim_stmt->close();
$suspect_stmt->close();
$witness_stmt->close();

// Close the database connection
$conn->close();
?>