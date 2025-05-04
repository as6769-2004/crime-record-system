<?php

include '../../includes/db_connect.php';

// Get only station_id and officer_id from URL
$station_id = isset($_GET['station_id']) ? intval($_GET['station_id']) : $default_station_id;
$officer_id = isset($_GET['officer_id']) ? intval($_GET['officer_id']) : $default_officer_id;

// Sanitize for HTML output
$station_id_safe = ($station_id !== null) ? intval($station_id) : null;
$officer_id_safe = ($officer_id !== null) ? intval($officer_id) : null;

$stmt = $conn->prepare("SELECT station_name FROM POLICE_STATION WHERE station_id = ?");
$stmt->bind_param("i", $station_id_safe); // Bind the station_id as an integer
$stmt->execute();
$result = $stmt->get_result();
$station_name_safe = "";

if ($row = $result->fetch_assoc()) {
    $station_name_safe = htmlspecialchars($row['station_name']);
} else {
    echo "station_name not found"; // Fallback if no result is found
}

$stmt = $conn->prepare("SELECT name FROM OFFICER WHERE officer_id = ?");
$stmt->bind_param("i", $officer_id_safe); // Bind the station_id as an integer
$stmt->execute();
$result = $stmt->get_result();
$name_safe = "";

if ($row = $result->fetch_assoc()) {
    $name_safe = htmlspecialchars($row['name']);
} else {
    echo "name not found"; // Fallback if no result is found
}
include 'headerlogged.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $station_name_safe; ?> - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/main_new.css">

</head>

<body>
    <div class="container_main">

        <div class="left-column">
            <h2><span id="menu-title">Menu</span></h2>
            <ul class="menu">
                <li><a href="#"
                        data-page="../menu_acess/station_info.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Station
                        Info</a></li>

                <li><a href="#"
                        data-page="../menu_acess/add_crime.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Add
                        New Crime</a></li>

                <li><a href="#"
                        data-page="../menu_acess/crime_lists.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Case
                        Status</a></li>

                <li><a href="#"
                        data-page="../menu_acess/case_log.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Case
                        Log</a></li>

                <li><a href="#"
                        data-page="../menu_acess/Officers_stations.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Officer</a>
                </li>

                <li><a href="#"
                        data-page="../menu_acess/witness.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Witness</a>
                </li>

                <li><a href="#"
                        data-page="../menu_acess/victim.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Victims</a>
                </li>

                <li><a href="#"
                        data-page="../menu_acess/suspect.php?station_name=<?php echo urlencode($station_name_safe); ?>&officer_id=<?php echo $officer_id_safe; ?>&name=<?php echo urlencode($name_safe); ?>">Suspect</a>
                </li>
            </ul>
            <div class="left_bottom">

                <div class="datetime" id="currentDateTime"></div>
                <a href="logout.php" class="logout-button">Logout</a>
            </div>
        </div>

        <div class="right-column">
            <div id="display-container">
                <div id="default-content">
                    <div class="welcome-message">
                        <?php echo "Welcome to <strong>" . $station_name_safe . "</strong>, Officer <strong>" . $name_safe . "</strong>"; ?>
                    </div>
                    <div id="news-section">
                        <h2>Latest News</h2>
                        <ul id="news-list" style="list-style: none; padding-left: 0;">
                            <?php
                            $newsItems = [
                                [
                                    "title" => "Breaking: Major tech company announces new AI advancements.",
                                    "image" => "https://via.placeholder.com/100x60?text=AI"
                                ],
                                [
                                    "title" => "Business News: Stock market sees slight fluctuations.",
                                    "image" => "https://via.placeholder.com/100x60?text=Stocks"
                                ],
                                [
                                    "title" => "Security Alert: New cyberattack targets government systems.",
                                    "image" => "https://via.placeholder.com/100x60?text=Cyber"
                                ],
                                [
                                    "title" => "Local: Community rally held for flood victims.",
                                    "image" => "https://via.placeholder.com/100x60?text=Local"
                                ],
                                [
                                    "title" => "Sports: Underdog team wins national championship.",
                                    "image" => "https://via.placeholder.com/100x60?text=Sports"
                                ],
                                [
                                    "title" => "Technology: Breakthrough in quantum computing announced.",
                                    "image" => "https://via.placeholder.com/100x60?text=Quantum"
                                ],
                                [
                                    "title" => "Politics: New policy changes spark nationwide debate.",
                                    "image" => "https://via.placeholder.com/100x60?text=Politics"
                                ],
                                [
                                    "title" => "Environment: Global emissions reach record low in April.",
                                    "image" => "https://via.placeholder.com/100x60?text=Eco"
                                ],
                                [
                                    "title" => "Health: Vaccine research shows promising results.",
                                    "image" => "https://via.placeholder.com/100x60?text=Health"
                                ],
                                [
                                    "title" => "International: Peace talks resume in conflict zone.",
                                    "image" => "https://via.placeholder.com/100x60?text=World"
                                ]
                            ];

                            foreach ($newsItems as $news) {
                                echo '<li style="display: flex; align-items: center; margin-bottom: 10px;">';
                                echo '<img src="' . htmlspecialchars($news['image']) . '" alt="News Image" style="width: 100px; height: 60px; margin-right: 10px;">';
                                echo '<span>' . htmlspecialchars($news['title']) . '</span>';
                                echo '</li>';
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