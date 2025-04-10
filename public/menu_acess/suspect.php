<?php
// Database connection
include('../../includes/db_connect.php');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Add Suspect
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_suspect"])) {
    $name = $_POST["name"];
    $address = $_POST["address"];
    $phone = $_POST["phone"];
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];
    $suspect_pic = null;

    if (!empty($_FILES["suspect_pic"]["tmp_name"])) {
        $ext = strtolower(pathinfo($_FILES["suspect_pic"]["name"], PATHINFO_EXTENSION));
        $img_data = file_get_contents($_FILES["suspect_pic"]["tmp_name"]);
        $img_hash = md5($img_data . time());
        $filename = $img_hash . "." . $ext;
        $target = "../../assets/pictures/" . $filename;

        if (move_uploaded_file($_FILES["suspect_pic"]["tmp_name"], $target)) {
            $suspect_pic = $filename;
        } else {
            echo "<script>alert('Error uploading image');</script>";
        }
    }

    $stmt = $conn->prepare("INSERT INTO suspect (name, address, phone, date_of_birth, gender, suspect_pic) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $address, $phone, $dob, $gender, $suspect_pic);

    if ($stmt->execute()) {
        echo "<script>alert('Suspect added successfully');</script>";
    } else {
        echo "<script>alert('Error adding suspect: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// View Suspects
$sql = "SELECT suspect_id, name, address, phone, date_of_birth, gender, suspect_pic FROM suspect";
$result = $conn->query($sql);

$suspects = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $suspects[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suspect Management</title>
    <link rel="stylesheet" href="../../assets/css/suspect.css">
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
    <div class="top-section">
        <h2>View Suspects</h2>
        <div class="action-buttons">
            <button id="addSuspectButton">Add Suspect</button>
            <button id="refreshButton" onclick="location.reload()">Refresh</button>
        </div>
    </div>

    <div class="main-content">
        <div id="suspectForm" style="display:none;">
            <h2>Add Suspect</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="name">Name:</label>
                <input type="text" name="name" required>

                <label for="address">Address:</label>
                <input type="text" name="address">

                <label for="phone">Phone:</label>
                <input type="text" name="phone">

                <label for="dob">Date of Birth:</label>
                <input type="date" name="dob">

                <label for="gender">Gender:</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>

                <label for="suspect_pic">Suspect Picture:</label>
                <input type="file" name="suspect_pic" accept="image/*">

                <button type="submit" name="add_suspect">Add Suspect</button>
            </form>
        </div>

        <div class="table">
            <?php if (!empty($suspects)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Picture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($suspects as $suspect) : ?>
                            <tr>
                                <td><?php echo $suspect['suspect_id']; ?></td>
                                <td><?php echo $suspect['name']; ?></td>
                                <td><?php echo $suspect['address']; ?></td>
                                <td><?php echo $suspect['phone']; ?></td>
                                <td><?php echo $suspect['date_of_birth']; ?></td>
                                <td><?php echo $suspect['gender']; ?></td>
                                <td>
                                    <?php if (!empty($suspect['suspect_pic'])) : ?>
                                        <img src="../../assets/pictures/<?php echo $suspect['suspect_pic']; ?>" 
                                             alt="Suspect Picture" 
                                             style="max-width: 100px; max-height: 100px; cursor: pointer;" 
                                             onclick="previewImage('../../assets/pictures/<?php echo $suspect['suspect_pic']; ?>')">
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>No suspects found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imagePreview">
    <img id="previewImage" src="" alt="Preview">
</div>

<script>
    document.getElementById('addSuspectButton').addEventListener('click', function() {
        var form = document.getElementById('suspectForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
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
