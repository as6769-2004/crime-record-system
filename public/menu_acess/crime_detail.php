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
?>

<!-- Your HTML Content -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Details</title>
    <!-- Add your CSS files -->
</head>

<body>
    <h1>Crime Details</h1>

    <h2>Crime Information</h2>
    <p><strong>Crime Type:</strong> <?php echo $crime['crime_type']; ?></p>
    <p><strong>Date:</strong> <?php echo $crime['crime_date']; ?></p>
    <p><strong>Location:</strong> <?php echo $crime['location']; ?></p>
    <p><strong>Description:</strong> <?php echo $crime['description']; ?></p>
    <p><strong>Status:</strong> <?php echo $crime['status']; ?></p>

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
    <?php endif; ?>

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
</body>

</html>

<?php
// Close the prepared statements
$stmt->close();
$evidence_stmt->close();
$logs_stmt->close();

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Crime Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: black;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <h1>Crime Details (Case: <?php echo htmlspecialchars($crime['case_number']); ?>)</h1>

    <h2>Crime Information</h2>
    <table>
        <tr>
            <th>Type</th>
            <td><?php echo htmlspecialchars($crime['crime_type']); ?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?php echo htmlspecialchars($crime['crime_date']); ?></td>
        </tr>
        <tr>
            <th>Location</th>
            <td><?php echo htmlspecialchars($crime['location']); ?></td>
        </tr>
        <tr>
            <th>Description</th>
            <td><?php echo nl2br(htmlspecialchars($crime['description'])); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo htmlspecialchars($crime['status']); ?></td>
        </tr>
    </table>

    <h2>Evidence</h2>
    <?php if ($evidence_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Description</th>
                <th>Location Found</th>
                <th>Date Collected</th>
                <th>File</th>
            </tr>
            <?php while ($evidence = $evidence_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($evidence['description']); ?></td>
                    <td><?php echo htmlspecialchars($evidence['location_found']); ?></td>
                    <td><?php echo htmlspecialchars($evidence['date_collected']); ?></td>
                    <td>
                        <?php if ($evidence['file_url']): ?>
                            <a href="<?php echo htmlspecialchars($evidence['file_url']); ?>" target="_blank">View File</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No evidence collected for this crime yet.</p>
    <?php endif; ?>

    <h2>Case Logs</h2>
    <?php if ($logs_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Officer</th>
                <th>Log Entry</th>
            </tr>
            <?php while ($log = $logs_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['log_date']); ?></td>
                    <td><?php echo htmlspecialchars($log['officer_name'] ?? 'N/A'); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($log['log_entry'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No case logs for this crime yet.</p>
    <?php endif; ?>

</body>

</html>

<?php
// Close all statements and connection
$crime_stmt->close();
$evidence_stmt->close();
$logs_stmt->close();
$mysqli->close();
?>