<?php
// Database connection (adjust credentials as needed)
include('../../includes/db_connect.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Victim
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_victim"])) {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $victim_pic = null; // Initialize to null

    // Handle image upload (if any)
    if (!empty($_FILES["victim_pic"]["tmp_name"])) {
        $imageFileType = strtolower(pathinfo($_FILES["victim_pic"]["name"], PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["victim_pic"]["tmp_name"]);
        if ($check !== false) {
            // No file type restriction now.
            // Generate a unique hash
            $image_data = file_get_contents($_FILES["victim_pic"]["tmp_name"]);
            $image_hash = md5($image_data);

            $target_dir = "../../assets/pictures/";
            $target_file = $target_dir . $image_hash . "." . $imageFileType;

            // Check if file with same hash exists
            if (!file_exists($target_file)) {
                if (move_uploaded_file($_FILES["victim_pic"]["tmp_name"], $target_file)) {
                    // Image uploaded successfully, save file path to database
                    $victim_pic = $image_hash . "." . $imageFileType;
                } else {
                    $error = error_get_last();
                    echo "<script>alert('Sorry, there was an error uploading your file: " . $error['message'] . "');</script>";
                }
            } else {
                $victim_pic = $image_hash . "." . $imageFileType;
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO VICTIM (name, address, phone, email, date_of_birth, gender, victim_pic) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $name, $address, $phone, $email, $dob, $gender, $victim_pic);

    if ($stmt->execute()) {
        echo "<script>alert('Victim added successfully');</script>";
    } else {
        echo "<script>alert('Error adding victim: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// View Victims
$sql = "SELECT victim_id, name, address, phone, email, date_of_birth, gender, victim_pic FROM VICTIM";
$result = $conn->query($sql);

$victims = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $victims[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Victim Management</title>
    <link rel="stylesheet" href="../../assets/css/victim.css">
    <style>
        #imagePreview {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        #imagePreview img {
            max-width: 90%;
            max-height: 90%;
        }
    </style>
</head>

<body>
    <div class="container">
        <button id="addVictimButton">Add Victim</button>
        <button id="refreshButton" onclick="location.reload()">Refresh Victims</button>

        <div id="victimForm">
            <h2>Add Victim</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" name="name" required>

                <label for="address">Address:</label>
                <input type="text" name="address">

                <label for="phone">Phone:</label>
                <input type="text" name="phone">

                <label for="email">Email:</label>
                <input type="email" name="email">

                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob">

                <label for="gender">Gender:</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="victim_pic">Victim Picture:</label>
                <input type="file" name="victim_pic" accept="image/*">

                <button type="submit" name="add_victim">Add Victim</button>
            </form>
        </div>

        <h2>View Victims</h2>
        <?php if (!empty($victims)) : ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Gender</th>
                        <th>Picture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($victims as $victim) : ?>
                        <tr>
                            <td><?php echo $victim['victim_id']; ?></td>
                            <td><?php echo $victim['name']; ?></td>
                            <td><?php echo $victim['address']; ?></td>
                            <td><?php echo $victim['phone']; ?></td>
                            <td><?php echo $victim['email']; ?></td>
                            <td><?php echo $victim['date_of_birth']; ?></td>
                            <td><?php echo $victim['gender']; ?></td>
                            <td>
                                <?php if (!empty($victim['victim_pic'])) : ?>
                                    <img src="../../assets/pictures/<?php echo $victim['victim_pic']; ?>" alt="Victim Picture" style="max-width: 100px; max-height: 100px;cursor: pointer;" onclick="previewImage('../../assets/pictures/<?php echo $victim['victim_pic']; ?>')">
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No victims found.</p>
        <?php endif; ?>
    </div>
    <div id="imagePreview">
        <img id="previewImage" src="" alt="Preview">
    </div>
    <script>
        document.getElementById('addVictimButton').addEventListener('click', function() {
            var form = document.getElementById('victimForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        });
        function previewImage(imageSrc) {
            document.getElementById('previewImage').src = imageSrc;
            document.getElementById('imagePreview').style.display = 'flex';
        }

        document.getElementById('imagePreview').addEventListener('click', function() {
            this.style.display = 'none';
        });
    </script>
</body>

</html>