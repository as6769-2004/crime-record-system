<?php
include '../../includes/db_connect.php';

$crime_id = $_GET['crime_id'] ?? 0;
$crime_id = intval($crime_id);  // Ensuring crime_id is valid integer
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Crime Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: rgba(18, 18, 18, 0.5);
            color: #ffffff;
        }

        .menu-button {
            margin-right: 10px;
        }

        iframe {
            width: 100%;
            height: 85vh;
            border: none;
            background-color: rgba(30, 30, 30, 0.5);
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>

    <script>
        function loadPage(page) {
            const crimeId = <?= $crime_id ?>;
            document.getElementById('content-frame').src = page + '?crime_id=' + crimeId;
        }
        // Automatically load Victims page by default when the page is first loaded
        window.onload = function () {
            loadPage('update_victim.php');
        }
    </script>
</head>

<body>
    <div class="container py-4">

        <h2 class="text-center mb-4">Manage Case #<?= htmlspecialchars($crime_id) ?> - Crime Details</h2>

        <!-- Menu Bar -->
        <div class="d-flex justify-content-center mb-4">
            <button class="btn btn-outline-info menu-button" onclick="loadPage('update_victim.php')">Victims</button>
            <button class="btn btn-outline-warning menu-button"
                onclick="loadPage('update_witness.php')">Witnesses</button>
            <button class="btn btn-outline-danger menu-button"
                onclick="loadPage('update_suspect.php')">Suspects</button>
            <button class="btn btn-outline-success menu-button" onclick="loadPage('evidence.php')">Add Evidence</button>
            <!-- Add this button -->
        </div>

        <!-- Container to load PHP pages -->
        <iframe id="content-frame" src=""></iframe>

    </div>
</body>

</html>