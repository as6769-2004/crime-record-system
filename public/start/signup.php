<?php
include 'headerfront.php';
include('../../includes/db_connect.php'); // Database connection

// Fetch officers from the OFFICER table
$query = "SELECT officer_id, name FROM OFFICER";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching officers: " . $conn->error);
}

// Handle signup form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $officer_id = $_POST['officer'];
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password

    // Prepare SQL statement to insert new user
    $stmt = $conn->prepare("INSERT INTO LOGIN_INFO (officer_id, username, password) VALUES (?, ?, ?)");

    if ($stmt) {
        $stmt->bind_param("iss", $officer_id, $username, $password);

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
    <title>Officer Account Registration - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/loginstyle.css">
</head>

<body>
    <h2>Officer Account Registration</h2>

    <div class="login-form">
        <form action="signup.php" method="POST">
            <label for="officer">Select Officer:</label>
            <select name="officer" id="officer" required>
                <option value="">--Select Officer--</option>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['officer_id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                }
                ?>
            </select><br>
            <input type="text" name="username" placeholder="Create Username" required><br>
            <input type="password" name="password" placeholder="Create Password" required><br>
            <input type="submit" value="Register Account"><br>
        </form>
        <p>Already have an account?</p> <a href="login.php">Login here</a></p>
    </div>
</body>

</html>