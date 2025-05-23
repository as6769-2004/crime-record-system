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

// Add Witness
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_witness"])) {
    // Collect data from the form
    $name = $_POST["name"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $email = $_POST["email"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $created_by = 1;  // Hardcoded for now; replace with actual user ID when implementing login
    $witness_pic = null; // Initialize to null

    // Handle image upload
    if (!empty($_FILES["witness_pic"]["tmp_name"])) {
        $imageFileType = strtolower(pathinfo($_FILES["witness_pic"]["name"], PATHINFO_EXTENSION));

        // Check if the file is an image
        $check = getimagesize($_FILES["witness_pic"]["tmp_name"]);
        if ($check !== false) {
            // Generate a unique hash for the image
            $image_data = file_get_contents($_FILES["witness_pic"]["tmp_name"]);
            $image_hash = md5($image_data);

            $target_dir = "../../assets/pictures/"; // Directory where images are stored
            $target_file = $target_dir . $image_hash . "." . $imageFileType;

            // Check if file with same hash exists
            if (!file_exists($target_file)) {
                if (move_uploaded_file($_FILES["witness_pic"]["tmp_name"], $target_file)) {
                    // If upload is successful, store the file name in the database
                    $witness_pic = $image_hash . "." . $imageFileType;
                } else {
                    echo "<script>alert('Error uploading file.');</script>";
                }
            } else {
                $witness_pic = $image_hash . "." . $imageFileType;
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    // Prepare the SQL query to insert the new witness
    $stmt = $conn->prepare("INSERT INTO WITNESS (name, address, phone, email, date_of_birth, gender, witness_pic, created_by) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $name, $address, $phone, $email, $dob, $gender, $witness_pic, $created_by);

    if ($stmt->execute()) {
        echo "<script>alert('Witness added successfully');</script>";
    } else {
        echo "<script>alert('Error adding witness: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// View Witnesses
$sql = "SELECT witness_id, name, address, phone, email, date_of_birth, gender, witness_pic FROM WITNESS";
$result = $conn->query($sql);

if ($result === false) {
    echo "Error executing query: " . $conn->error;
    exit; // Stop the script if there’s an issue with the query
}

$witnesses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $witnesses[] = $row;
    }
} else {
    echo "No witnesses found.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witness Management</title>
    <link rel="stylesheet" href="../../../crime-record-system/assets/css/witness.css">
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
    <!-- Top Section: Heading and Buttons -->
    <div class="top-section">
        <h2>View Witnesses</h2>
        <div class="action-buttons">
            <button id="addWitnessButton">Add Witness</button>
            <button id="refreshButton" onclick="window.location.href='witness.php'">Refresh</button>
        </div>
    </div>
    <div class="main-content">
        <div id="witnessForm">
            <h2>Add Witness</h2>
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
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                <label for="witness_pic">Witness Picture:</label>
                <input type="file" name="witness_pic" accept="image/*">

                <button type="submit" name="add_witness">Add Witness</button>
            </form>
        </div>

        <!-- Witness Table -->
        <div class="table">
            <?php if (!empty($witnesses)) : ?>
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
                        <?php foreach ($witnesses as $witness) : ?>
                            <tr>
                                <td><?php echo $witness['witness_id']; ?></td>
                                <td><?php echo $witness['name']; ?></td>
                                <td><?php echo $witness['address']; ?></td>
                                <td><?php echo $witness['phone']; ?></td>
                                <td><?php echo $witness['email']; ?></td>
                                <td><?php echo $witness['date_of_birth']; ?></td>
                                <td><?php echo ucfirst($witness['gender']); ?></td>
                                <td>
                                    <?php if (!empty($witness['witness_pic'])) : ?>
                                        <img src="../../assets/pictures/<?php echo $witness['witness_pic']; ?>"
                                            alt="Witness Picture"
                                            style="max-width: 100px; max-height: 100px; cursor: pointer;"
                                            onclick="previewImage('../../assets/pictures/<?php echo $witness['witness_pic']; ?>')">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No witnesses found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreview">
    <img id="previewImage" src="" alt="Preview">
</div>

<script>
    document.getElementById('addWitnessButton').addEventListener('click', function() {
        var form = document.getElementById('witnessForm');
        form.style.display = (form.style.display === 'none') ? 'block' : 'none';
    });

    function previewImage(imageSrc) {
        document.getElementById('previewImage').src = imageSrc;
        document.getElementById('imagePreview').style.display = 'flex';
    }

    document.getElementById('imagePreview').addEventListener('click', function() {
        document.getElementById('imagePreview').style.display = 'none';
    });
</script>

</body>
</html>
