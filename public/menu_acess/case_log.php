<?php
include '../../includes/db_connect.php';

$query = "SELECT * FROM case_logs ORDER BY log_date DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Case Logs - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/case_logs.css">
</head>

<body>
    <div class="container">

        <h2>Case Logs</h2>
        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Crime ID</th>
                    <th>Log Date</th>
                    <th>Log Entry</th>
                    <th>Officer ID</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["log_id"]) ?></td>
                            <td><?= htmlspecialchars($row["crime_id"]) ?></td>
                            <td><?= htmlspecialchars($row["log_date"]) ?></td>
                            <td><?= nl2br(htmlspecialchars($row["log_entry"])) ?></td>
                            <td><?= htmlspecialchars($row["officer_id"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No case logs found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</body>

</html>
<?php $conn->close(); ?>