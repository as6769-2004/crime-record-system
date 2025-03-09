<?php
include 'headerfront.php';
include('../../includes/db_connect.php'); // Database connection

// Fetch police stations from the PoliceStation table
$query = "SELECT Station_ID, Name FROM PoliceStation";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching police stations: " . $conn->error);
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $station_id = $_POST['policestation'];
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password

    // Prepare SQL statement to insert new user
    $stmt = $conn->prepare("INSERT INTO LoginInfo (Station_ID, User_Name, Password) VALUES (?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("iss", $station_id, $username, $password);

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Sign-up successful! You can now <a href='login.php'>login</a>.</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red;'>Error in SQL preparation.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/loginstyle.css">
</head>

<body>

    <div class="login-form">
        <form action="signup.php" method="POST">
            <label for="police-station">Select Police Station:</label>
            <select name="policestation" id="police-station" required>
                <option value="">--Select Police Station--</option>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['Station_ID'] . "'>" . htmlspecialchars($row['Name']) . "</option>";
                }
                ?>
            </select><br>
            <input type="text" name="username" placeholder="Create Username" required><br>
            <input type="password" name="password" placeholder="Create Password" required><br>
            <input type="submit" value="Sign Up"><br>
        </form>
        <p>Already have an account?</p> <a href="login.php">Login here</a></p>
    </div>
</body>

</html>