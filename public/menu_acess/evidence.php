<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id); // Ensuring crime_id is a valid integer

// Get current timestamp
$date_collected = date('Y-m-d H:i:s');

// Handle form submission for uploading evidence
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'] ?? '';  // Using null coalescing operator to avoid undefined index warnings
    $location_found = $_POST['location_found'] ?? '';  // Using null coalescing operator

    // Insert evidence record into the evidence table
    $stmt = $conn->prepare("INSERT INTO evidence (crime_id, description, location_found, created_by, date_collected) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $crime_id, $description, $location_found, $_SESSION['user_id'], $date_collected);
    $stmt->execute();

    $evidence_id = $stmt->insert_id;  // Get the last inserted evidence_id

    // Process each uploaded file
    if (isset($_FILES['evidence_files']) && count($_FILES['evidence_files']['name']) > 0) {
        $file_count = count($_FILES['evidence_files']['name']);

        for ($i = 0; $i < $file_count; $i++) {
            $file_name = $_FILES['evidence_files']['name'][$i];
            $file_tmp = $_FILES['evidence_files']['tmp_name'][$i];
            $file_size = $_FILES['evidence_files']['size'][$i];
            $file_error = $_FILES['evidence_files']['error'][$i];

            if ($file_error === UPLOAD_ERR_OK) {
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4'];

                if (in_array($file_ext, $allowed_extensions)) {
                    $file_new_name = uniqid('', true) . '.' . $file_ext;
                    $file_destination = '../../assets/files/' . $file_new_name;

                    if (move_uploaded_file($file_tmp, $file_destination)) {
                        // Insert file information into evidence_file table
                        $stmt = $conn->prepare("INSERT INTO evidence_file (evidence_id, file_url, file_type, description) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("isss", $evidence_id, $file_destination, $file_ext, $description);
                        $stmt->execute();
                    } else {
                        echo "Error uploading file: $file_name";
                    }
                } else {
                    echo "Invalid file type: $file_name";
                }
            } else {
                echo "Error with file upload: $file_name";
            }
        }
    }
}

// Delete file logic
if (isset($_GET['delete_file_id'])) {
    $file_id = $_GET['delete_file_id'];
    $stmt = $conn->prepare("SELECT file_url FROM evidence_file WHERE file_id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $file = $result->fetch_assoc();
        $file_path = $file['file_url'];

        // Delete the file from the server
        if (unlink($file_path)) {
            // Remove file entry from the database
            $stmt = $conn->prepare("DELETE FROM evidence_file WHERE file_id = ?");
            $stmt->bind_param("i", $file_id);
            $stmt->execute();
            echo "File deleted successfully.";
        } else {
            echo "Error deleting file.";
        }
    } else {
        echo "File not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evidence Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color:rgb(33, 37, 41);
            color: white;
        }

        .container {
            margin-top: 30px;
        }

        .card {
            margin-top: 20px;
        }

        .btn-upload {
            margin-top: 10px;
        }

        .list-group-item {
            background-color: #444;
            border: 1px solid #555;
        }

        .btn-outline-info {
            border-color: #17a2b8;
            color: #17a2b8;
        }

        .btn-outline-info:hover {
            background-color: #17a2b8;
            color: #fff;
        }

        .btn-outline-danger {
            border-color: #dc3545;
            color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: #fff;
        }

        /* Custom styles for the centered heading */
        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="mb-4">Manage Evidence for Case #<?= htmlspecialchars($crime_id) ?></h2>
        <!-- Form for adding evidence -->
        <form action="evidence.php?crime_id=<?php echo $crime_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="evidence_description" class="form-label">Description</label>
                <textarea id="evidence_description" name="description" class="form-control" rows="3"
                    required></textarea>
            </div>
            <div class="mb-3">
                <label for="location_found" class="form-label">Location Found</label>
                <input type="text" id="location_found" name="location_found" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="evidence_file" class="form-label">Upload Evidence Files</label>
                <input type="file" name="evidence_files[]" class="form-control" multiple required>
            </div>

            <button type="submit" class="btn btn-primary btn-upload">Submit Evidence</button>
        </form>

        <!-- Displaying existing evidence files -->
        <div class="mt-4">
            <h4>Uploaded Evidence Files</h4>
            <?php
            // Fetching and displaying evidence files for the given crime_id
            $stmt = $conn->prepare("SELECT ef.file_id, ef.file_url, ef.file_type, ef.description FROM evidence_file ef INNER JOIN evidence e ON ef.evidence_id = e.evidence_id WHERE e.crime_id = ?");
            $stmt->bind_param("i", $crime_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="list-group">';
                while ($file = $result->fetch_assoc()) {
                    echo '<div class="list-group-item">';
                    echo '<p>' . htmlspecialchars($file['description']) . '</p>';
                    echo '<a href="' . htmlspecialchars($file['file_url']) . '" target="_blank" class="btn btn-outline-info">Download File</a>';
                    echo '<form action="evidence.php" method="GET" style="display:inline;">';
                    echo '<input type="hidden" name="delete_file_id" value="' . htmlspecialchars($file['file_id']) . '">';
                    echo '<button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm(\'Are you sure?\')">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>No evidence files uploaded yet.</p>';
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
