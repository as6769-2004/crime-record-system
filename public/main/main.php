<?php
include 'headerlogged.php';

session_start();

// Check if Station_Name is set. If not, redirect to the login page.
if (!isset($_SESSION['Station_Name'])) {
    // For development/testing ONLY.  Do NOT use in production.
    $station_name = 'Demo For Development';
    // You might also want to log this for debugging:
    error_log("WARNING: Station_Name not set. Using demo value.");
} else {
    $station_name = $_SESSION['Station_Name'];
}

//$station_name = $_SESSION['Station_Name']; // It's safe to access this now

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(isset($station_name) ? $station_name : 'Crime Record System'); ?> - Crime Record System</title>
    <link rel="stylesheet" href="../../assets/css/main.css">
    <style>
        #default-content {
            padding: 20px;
            text-align: center;
            font-size: 1.2em;
        }

        #contentFrame {
            /* Style to hide the iframe initially */
            display: none;
            width: 100%;
            height: 600px;
            /* Or adjust as needed */
            border: none;
            /* Remove default border */
        }
    </style>
</head>

<body><div class="container_u_main">
    <div class="container_main">

        <div class="left-column">
            <h2><span id="menu-title">Menu</span></h2>
            <ul class="menu">
                <li><a href="#" data-page="../menu_acess/station_info.php?station=<?php echo urlencode($station_name); ?>">Station Info</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/add_crime.php">Add New Crime</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/case_status.php">Case Status</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/case_log.php">Case Log</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/Officers_stations.php">Officer</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/Officers_stations.php">Witness</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/Officers_stations.php">Victims</a></li>
                <hr>
                <li><a href="#" data-page="../menu_acess/Officers_stations.php">Optional</a></li>
                <hr>
                <li><a href="#" data-page="optional.html">Optional</a></li>
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
                    <?php echo "Welcome to " . htmlspecialchars($station_name); ?>

                    <div id="news-section">
                        <h2>Latest News</h2>
                        <ul id="news-list">
                            <?php
                            // Example PHP code to fetch and display news (replace with actual logic)

                            // Simulate news items (replace with real data retrieval)
                            $newsItems = [
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
                                "Business News: Stock market sees slight fluctuations.",
                                "Breaking: Major tech company announces new AI advancements.",
                                "Local Weather: Heavy rain expected in the region.",
                                "Sports Update: Exciting cricket match results.",
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
        const menuTitle = document.getElementById('menu-title'); // Get the menu title element

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

        // Add event listener to the menu title
        menuTitle.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent any default action of the link
            contentFrame.style.display = "none";
            defaultContent.style.display = "block";
        });

        function updateDateTime() {
            const now = new Date();
            const options = {
                year: "numeric", // Display the year
                month: "long", // Display the full month name
                day: "numeric", // Display the day of the month
                hour: "numeric", // Display the hour
                minute: "numeric", // Display the minutes
                second: "numeric", // Display the seconds
                hour12: true // Use 12-hour format (true) or 24-hour format (false)
            };
            const dateTimeString = now.toLocaleDateString(undefined, options);
            document.getElementById("currentDateTime").textContent = dateTimeString;
        }
        setInterval(updateDateTime, 1000);
        updateDateTime();


        // This is the improved part to handle initial load
        window.addEventListener('DOMContentLoaded', (event) => {
            // Check if there is any page in the iframe
            if (contentFrame.src) {
                defaultContent.style.display = "none";
                contentFrame.style.display = "block";
            }
        });
        // window.addEventListener('beforeunload', (event) => {
        //     navigator.sendBeacon('logout.php', 'logout=true'); // Non-blocking request
        // });

        // TO LOGOUT WHEN WINDOW CLOSED
    </script>

</body>

</html>