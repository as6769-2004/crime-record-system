<?php
include 'headerlogged.php';

session_start();

// Check if Station_Name is set. If not, redirect to the login page.
if (!isset($_SESSION['Station_Name'])) {

    $station_name = 'Demo For Development';
    error_log("WARNING: Station_Name not set. Using demo value.");
} else {
    $station_name = $_SESSION['Station_Name'];
}

// Retrieve station_name, officer_id, and officer_name from the URL or session
$station_name_url = isset($_GET['station_name']) ? urldecode($_GET['station_name']) : $station_name;
$officer_id = isset($_GET['officer_id']) ? intval($_GET['officer_id']) : (isset($_SESSION['officer_id']) ? $_SESSION['officer_id'] : null);
$officer_name = isset($_SESSION['officer_name']) ? $_SESSION['officer_name'] : "Unknown Officer"; // Get officer name from session

// Sanitize the station name.
$station_name_safe = htmlspecialchars($station_name_url);

// Sanitize officer_id
$officer_id_safe = ($officer_id !== null) ? intval($officer_id) : null;

// Sanitize officer name
$officer_name_safe = htmlspecialchars($officer_name);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $station_name_safe; ?> - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        #default-content {
            padding: 20px;
            text-align: center;
            font-size: 1.2em;
        }

        #contentFrame {
            display: none;
            width: 100%;
            height: 600px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="container_u_main">
        <div class="container_main">

            <div class="left-column">
                <h2><span id="menu-title">Menu</span></h2>
                <ul class="menu">
                    <li><a href="#" data-page="../menu_acess/station_info.php?station=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>">Station Info</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/add_crime.php?officer_id=<?php echo $officer_id_safe; ?>">Add New Crime</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/case_status.php?officer_id=<?php echo $officer_id_safe; ?>">Case Status</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/case_log.php?officer_id=<?php echo $officer_id_safe; ?>">Case Log</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/Officers_stations.php?officer_id=<?php echo $officer_id_safe; ?>">Officer</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/witness.php?officer_id=<?php echo $officer_id_safe; ?>">Witness</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/victim.php?officer_id=<?php echo $officer_id_safe; ?>">Victims</a></li>
                    <hr>
                    <li><a href="#" data-page="../menu_acess/Officers_stations.php?officer_id=<?php echo $officer_id_safe; ?>">Optional</a></li>
                    <hr>
                    <li><a href="#" data-page="optional.html?officer_id=<?php echo $officer_id_safe; ?>">Optional</a></li>
                </ul>
                <div class="left_bottom">
                    <hr>
                    <div class="datetime" id="currentDateTime"></div>
                    <a href="logout.php" class="logout-button">Logout</a>
                </div>
            </div>
            <div class="right-column">
                <div id="display-container">
                    <div id="default-content">
                        <?php echo "Welcome to " . $station_name_safe . ", Officer " . $officer_name_safe; ?>

                        <div id="news-section">
                            <h2>Latest News</h2>
                            <ul id="news-list">
                                <?php
                                $newsItems = [
                                    "Breaking: Major tech company announces new AI advancements.",
                                    "Business News: Stock market sees slight fluctuations."
                                ];

                                foreach ($newsItems as $news) {
                                    echo "<li>" . htmlspecialchars($news) . "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <iframe id="contentFrame"></iframe>
                </div>
            </div>
        </div>

        <script>
            const menuLinks = document.querySelectorAll('.menu a');
            const contentFrame = document.getElementById('contentFrame');
            const defaultContent = document.getElementById('default-content');
            const menuTitle = document.getElementById('menu-title');

            menuLinks.forEach(link => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    const pageUrl = link.dataset.page;

                    if (pageUrl) {
                        contentFrame.src = pageUrl;
                        contentFrame.style.display = "block";
                        defaultContent.style.display = "none";
                    } else {
                        contentFrame.style.display = "none";
                        defaultContent.style.display = "block";
                    }
                });
            });

            menuTitle.addEventListener('click', (event) => {
                event.preventDefault();
                contentFrame.style.display = "none";
                defaultContent.style.display = "block";
            });

            function updateDateTime() {
                const now = new Date();
                const options = {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                    hour: "numeric",
                    minute: "numeric",
                    second: "numeric",
                    hour12: true
                };
                const dateTimeString = now.toLocaleDateString(undefined, options);
                document.getElementById("currentDateTime").textContent = dateTimeString;
            }
            setInterval(updateDateTime, 1000);
            updateDateTime();

            window.addEventListener('DOMContentLoaded', (event) => {
                if (contentFrame.src) {
                    defaultContent.style.display = "none";
                    contentFrame.style.display = "block";
                }
            });
        </script>

</body>

</html>