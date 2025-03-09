<?php
include 'headerfront.php';
include('../../includes/db_connect.php');

session_start(); // Start the session at the very beginning

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $policestation = $_POST['policestation'];

    if (empty($policestation)) {
        $error = "Please select a police station.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM LoginInfo WHERE User_Name = ? AND Station_ID = ?");

        if (!$stmt) {
            die("SQL Error: " . $conn->error);
        }

        $stmt->bind_param("si", $username, $policestation);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['Password'])) {
                $_SESSION['Station_ID'] = $user['Station_ID'];
                $_SESSION['username'] = $user['User_Name'];
                $_SESSION['Name'] = $user['Name'];

                // Fetch and store station name in session
                $stmt_station = $conn->prepare("SELECT Name FROM PoliceStation WHERE Station_ID = ?");
                $stmt_station->bind_param("i", $user['Station_ID']);
                $stmt_station->execute();
                $result_station = $stmt_station->get_result();

                if ($result_station->num_rows > 0) {
                    $station = $result_station->fetch_assoc();
                    $_SESSION['Station_Name'] = $station['Name'];
                }
                $stmt_station->close();

                header('Location: ../main/main.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username, password, or police station.";
        }
        $stmt->close();
    }
}

$query = "SELECT Station_ID, Name FROM PoliceStation";
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
    <link rel="stylesheet" href="..\..\assets\css\loginstyle.css">
</head>

<body>

    <div class="login-form">
        <form action="login.php" method="POST">
            <label for="police-station">Select the Police Station:</label>
            <select name="policestation" id="police-station" required>
                <option value="">--Select Police Station--</option>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['Station_ID'] . "'>" . $row['Name'] . "</option>";
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