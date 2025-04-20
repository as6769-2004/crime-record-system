<?php
include 'headerfront.php';
include('../../includes/db_connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $station_id = $_POST['police_station'];

    if (empty($station_id)) {
        $error = "Please select a police station.";
    } else {
        $stmt = $conn->prepare("SELECT li.*, o.name AS officer_name, ps.station_name, o.officer_id, ps.station_id 
                                FROM LOGIN_INFO li
                                INNER JOIN OFFICER o ON li.officer_id = o.officer_id
                                INNER JOIN POLICE_STATION ps ON o.station_id = ps.station_id
                                WHERE li.username = ? AND o.station_id = ?");

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("si", $username, $station_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if ($password === $user['password']) {
                if ($user['account_status'] === 'inactive') {
                    $error = "Account is inactive. Please contact admin.";
                } elseif ($user['account_status'] === 'locked') {
                    $error = "Account is locked. Contact system administrator.";
                } else {
                    // Account is active - proceed
                    $_SESSION['station_id'] = $user['station_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['officer_id'] = $user['officer_id']; // Only send officer_id and station_id

                    // Update last login timestamp
                    $updateStmt = $conn->prepare("UPDATE LOGIN_INFO SET last_login = NOW() WHERE login_id = ?");
                    $updateStmt->bind_param("i", $user['login_id']);
                    $updateStmt->execute();
                    $updateStmt->close();

                    //      // Redirect with only officer_id and station_id
                    header('Location: ../main/main.php?station_id=' . $user['station_id'] . '&officer_id=' . $user['officer_id']);
                    exit;
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "Invalid username or police station.";
        }

        $stmt->close();
    }
}

// Fetch police stations
$query = "SELECT station_id, station_name FROM POLICE_STATION";
$result = $conn->query($query);
if (!$result) {
    die("Error fetching police stations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Record System - Login</title>
    <link rel="stylesheet" href="../../assets/css/loginstyle.css">
</head>

<body>
    <div class="login-form">
        <form action="login.php" method="POST">
            <label for="police-station">Select the Police Station:</label>
            <select name="police_station" id="police-station" required>
                <option value="">--Select Police Station--</option>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['station_id'] . "'>" . htmlspecialchars($row['station_name']) . "</option>";
                }
                ?>
            </select><br>
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Login"><br>

            <?php
            if (isset($error)) {
                echo "<p style='color: red;'>" . htmlspecialchars($error) . "</p>";
            }
            ?>
        </form>
        <p>Don't have an account?</p>
        <a href="signup.php">Signup Here</a>
    </div>
</body>

</html>