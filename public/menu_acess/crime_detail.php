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

// // Query to get associated evidence for this crime
// // Prepare the query to fetch evidence file details by crime_id
// $evidence_query = "
//     SELECT ef.file_url, ef.file_type, ef.description
//     FROM evidence e
//     JOIN evidence_file ef ON e.evidence_id = ef.evidence_id
//     WHERE e.crime_id = ?";
// $evidence_stmt = $conn->prepare($evidence_query);

// // Check if the statement was prepared successfully
// if ($evidence_stmt === false) {
//     die('Error preparing the statement: ' . $conn->error);
// }

// // Bind the parameter to the prepared statement
// $evidence_stmt->bind_param("i", $crime_id);  // 'i' indicates the parameter is an integer

// // Check if binding was successful
// if ($evidence_stmt->error) {
//     die('Error binding the parameter: ' . $evidence_stmt->error);
// }

// // Execute the prepared statement
// $evidence_stmt->execute();

// // Check if execution was successful
// if ($evidence_stmt->error) {
//     die('Error executing the query: ' . $evidence_stmt->error);
// }

// // Get the result from the executed query
// $evidence_result = $evidence_stmt->get_result();

// // Check if any records were returned
// if ($evidence_result->num_rows > 0) {
//     // Output the evidence details
//     while ($evidence = $evidence_result->fetch_assoc()) {
//         // Check if 'file_url' key exists in the row
//         if (isset($evidence['file_url'])) {
//             $file_url = $evidence['file_url'];
//             $file_type = $evidence['file_type'];
//             $description = $evidence['description'];

//             // Process the evidence file information
//             echo "<div>";
//             echo "<p><strong>File URL:</strong> " . $file_url . "</p>";
//             echo "<p><strong>File Type:</strong> " . $file_type . "</p>";
//             echo "<p><strong>Description:</strong> " . $description . "</p>";
//             echo "<a href='$file_url' target='_blank'>Click to view the file</a>";
//             echo "</div><br>";
//         } else {
//             echo "Evidence file URL not found for this record.<br>";
//         }
//     }
// } else {
//     echo "No evidence found for this crime ID.<br>";
// }

// // Close the statement after use

// Query to get associated case logs for this crime
$logs_query = "SELECT * FROM case_logs WHERE crime_id = ?";
$logs_stmt = $conn->prepare($logs_query);
$logs_stmt->bind_param("i", $crime_id);
$logs_stmt->execute();
$logs_result = $logs_stmt->get_result();

// Query to get victims related to this crime
$victim_query = "
    SELECT v.victim_id, v.name, v.address, v.phone, v.email, v.date_of_birth, v.gender, v.victim_pic
    FROM crime_victims cv
    JOIN victim v ON cv.victim_id = v.victim_id
    WHERE cv.crime_id = ?";
$victim_stmt = $conn->prepare($victim_query);
$victim_stmt->bind_param("i", $crime_id);
$victim_stmt->execute();
$victim_result = $victim_stmt->get_result();

// Query to get suspects related to this crime
$suspect_query = "
    SELECT s.suspect_id, s.name, s.address, s.phone, s.date_of_birth, s.gender, s.suspect_pic, s.known_offender
    FROM crime_suspects cs
    JOIN suspect s ON cs.suspect_id = s.suspect_id
    WHERE cs.crime_id = ?";
$suspect_stmt = $conn->prepare($suspect_query);
$suspect_stmt->bind_param("i", $crime_id);
$suspect_stmt->execute();
$suspect_result = $suspect_stmt->get_result();

// Query to get witnesses related to this crime
$witness_query = "
    SELECT w.witness_id, w.name, w.address, w.phone, w.email, w.date_of_birth, w.gender, w.witness_pic
    FROM crime_witnesses cw
    JOIN witness w ON cw.witness_id = w.witness_id
    WHERE cw.crime_id = ?";
$witness_stmt = $conn->prepare($witness_query);
$witness_stmt->bind_param("i", $crime_id);
$witness_stmt->execute();
$witness_result = $witness_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Details</title>
    <style>
        /* Dark Theme Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #e0e0e0;
            margin: 0;
            padding: 0;
        }

        h1,
        h2 {
            color: #f1f1f1;
        }

        h2 {
            margin-top: 20px;
            text-decoration: underline;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
            padding: 20px;
        }

        .tile {
            background-color: #1e1e1e;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .tile:hover {
            transform: scale(1.05);
        }

        .tile img {
            max-width: 100%;
            border-radius: 5px;
        }

        .tile strong {
            color: #f1f1f1;
        }

        a button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        a button:hover {
            background-color: #45a049;
        }

        .tile p {
            font-size: 14px;
            margin: 5px 0;
        }

        .tile ul {
            padding-left: 20px;
            list-style-type: none;
        }

        .tile li {
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="tile">
            <h2>Crime Information</h2>
            <p><strong>Crime Type:</strong> <?php echo $crime['crime_type']; ?></p>
            <p><strong>Date:</strong> <?php echo $crime['crime_date']; ?></p>
            <p><strong>Location:</strong> <?php echo $crime['location']; ?></p>
            <p><strong>Description:</strong> <?php echo $crime['description']; ?></p>
            <p><strong>Status:</strong> <?php echo $crime['status']; ?></p>
        </div>

        <div class="tile">
            <h2>Victims</h2>
            <?php if ($victim_result->num_rows > 0): ?>
                <ul>
                    <?php while ($victim = $victim_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $victim['name']; ?><br>
                            <strong>Address:</strong> <?php echo $victim['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $victim['phone']; ?><br>
                            <strong>Email:</strong> <?php echo $victim['email']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $victim['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($victim['gender']); ?><br>
                            <?php if (!empty($victim['victim_pic'])): ?>
                                <img src="<?php echo $victim['victim_pic']; ?>" alt="Victim Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No victims found for this crime.</p>
            <?php endif; ?>
        </div>

        <div class="tile">
            <h2>Suspects</h2>
            <?php if ($suspect_result->num_rows > 0): ?>
                <ul>
                    <?php while ($suspect = $suspect_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $suspect['name']; ?><br>
                            <strong>Address:</strong> <?php echo $suspect['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $suspect['phone']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $suspect['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($suspect['gender']); ?><br>
                            <strong>Known Offender:</strong> <?php echo $suspect['known_offender'] ? 'Yes' : 'No'; ?><br>
                            <?php if (!empty($suspect['suspect_pic'])): ?>
                                <img src="<?php echo $suspect['suspect_pic']; ?>" alt="Suspect Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No suspects found for this crime.</p>
            <?php endif; ?>
        </div>

        <div class="tile">
            <h2>Witnesses</h2>
            <?php if ($witness_result->num_rows > 0): ?>
                <ul>
                    <?php while ($witness = $witness_result->fetch_assoc()): ?>
                        <li>
                            <strong>Name:</strong> <?php echo $witness['name']; ?><br>
                            <strong>Address:</strong> <?php echo $witness['address']; ?><br>
                            <strong>Phone:</strong> <?php echo $witness['phone']; ?><br>
                            <strong>Email:</strong> <?php echo $witness['email']; ?><br>
                            <strong>Date of Birth:</strong> <?php echo $witness['date_of_birth']; ?><br>
                            <strong>Gender:</strong> <?php echo ucfirst($witness['gender']); ?><br>
                            <?php if (!empty($witness['witness_pic'])): ?>
                                <img src="<?php echo $witness['witness_pic']; ?>" alt="Witness Picture"><br>
                            <?php endif; ?>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No witnesses found for this crime.</p>
            <?php endif; ?>
        </div>
        <div class="tile">
            <h2>Evidence</h2>
            <?php
            // Query to get associated evidence for this crime
            $evidence_query = "
        SELECT ef.file_url, ef.file_type, ef.description
        FROM evidence e
        JOIN evidence_file ef ON e.evidence_id = ef.evidence_id
        WHERE e.crime_id = ?";
            $evidence_stmt = $conn->prepare($evidence_query);

            // Check if the statement was prepared successfully
            if ($evidence_stmt === false) {
                die('Error preparing the statement: ' . $conn->error);
            }

            // Bind the parameter to the prepared statement
            $evidence_stmt->bind_param("i", $crime_id);  // 'i' indicates the parameter is an integer
            
            // Execute the prepared statement
            $evidence_stmt->execute();

            // Get the result from the executed query
            $evidence_result = $evidence_stmt->get_result();

            // Initialize array to cache file URLs
            $file_urls = [];

            // Check if any records were returned
            if ($evidence_result->num_rows > 0): ?>
                <ul style="display: flex; flex-wrap: wrap; gap: 15px; list-style: none; padding: 0;">
                    <?php
                    while ($evidence = $evidence_result->fetch_assoc()):
                        $file_url = $evidence['file_url'];
                        $file_ext = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
                        $icon = "";

                        // Cache file URL for preview
                        $file_urls[] = $file_url;

                        // Determine the file type icon based on file extension
                        if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $icon = "<img src='$file_url' alt='evidence image' style='width:60px; height:60px; border-radius:5px; cursor:pointer;' onclick='showPreview(\"$file_url\", \"$file_ext\")'>";
                        } elseif (in_array($file_ext, ['mp4', 'webm'])) {
                            $icon = "<img src='../../icons/video_icon.png' alt='video icon' style='width:60px; height:60px; cursor:pointer;' onclick='showPreview(\"$file_url\", \"$file_ext\")'>";
                        } elseif ($file_ext === 'pdf') {
                            $icon = "<img src='../../icons/pdf_icon.png' alt='pdf icon' style='width:60px; height:60px; cursor:pointer;' onclick='showPreview(\"$file_url\", \"$file_ext\")'>";
                        } else {
                            $icon = "<img src='../../icons/file_icon.png' alt='file icon' style='width:60px; height:60px; cursor:pointer;' onclick='showPreview(\"$file_url\", \"$file_ext\")'>";
                        }
                        ?>
                        <li><?php echo $icon; ?></li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No evidence found for this crime.</p>
            <?php endif; ?>

            <?php
            // Close the statement after use
            if ($evidence_stmt) {
                $evidence_stmt->close();
            }
            ?>
        </div>

        <div id="previewModal"
            style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background-color:rgba(0,0,0,0.8); z-index:1000; justify-content:center; align-items:center;">
            <div id="previewContent"
                style="max-width:90%; max-height:90%; background:#1e1e1e; padding:10px; border-radius:10px; text-align:center; position:relative;">
                <span onclick="closePreview()"
                    style="position:absolute; top:10px; right:15px; color:#fff; font-size:24px; cursor:pointer;">&times;</span>
                <div id="previewArea"></div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="crime_update_pages.php?crime_id=<?= $crime_id ?>">
                <button>Take Action</button>
            </a>
        </div>

        <script>
            // Cache file URLs in an array from PHP
            var cachedFiles = <?php echo json_encode($file_urls); ?>;

            // Function to show preview of the clicked file
            function showPreview(file_url, file_ext) {
                var previewModal = document.getElementById('previewModal');
                var previewArea = document.getElementById('previewArea');

                // Clear previous content
                previewArea.innerHTML = '';

                // Check if the file URL exists in the cached list (optional, but good for security)
                if (!cachedFiles.includes(file_url)) {
                    alert("File URL not found in the cache!");
                    return;
                }

                // Check the file extension and display the appropriate preview
                if (file_ext === 'jpg' || file_ext === 'jpeg' || file_ext === 'png' || file_ext === 'gif') {
                    previewArea.innerHTML = '<img src="' + file_url + '" style="max-width:100%; max-height:100%;">';
                } else if (file_ext === 'mp4' || file_ext === 'webm') {
                    previewArea.innerHTML = '<video controls style="max-width:100%; max-height:100%"><source src="' + file_url + '" type="video/' + file_ext + '"></video>';
                } else if (file_ext === 'pdf') {
                    // For better PDF rendering, consider using a PDF viewer library or embedding
                    previewArea.innerHTML = '<iframe src="' + file_url + '" style="width:100%; height:100%;"></iframe>';
                } else {
                    previewArea.innerHTML = '<p>Preview not available for this file type.</p>';
                }

                // Show the preview modal
                previewModal.style.display = 'flex';
            }

            // Close the preview modal
            function closePreview() {
                var previewModal = document.getElementById('previewModal');
                previewModal.style.display = 'none';
            }
        </script>

</html>


<?php
// Close the prepared statements
$stmt->close();
$logs_stmt->close();
$victim_stmt->close();
$suspect_stmt->close();
$witness_stmt->close();

// Close the database connection
$conn->close();
?>