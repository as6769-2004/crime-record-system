<?php
session_start();
if (!isset($_SESSION['station_name'])) {
    $_SESSION['station_name'] = "Default Station"; // Set a default value to prevent errors
}
$station_name = $_SESSION['station_name']; // Retrieve the police station name
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($station_name); ?> - Crime Record System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        .navbar {

            align-items: left;
            /* Vertically align items */
        }

        .navbar-brand {
            display: flex;
            /* Use flexbox for the brand */
            align-items: center;
            /* Vertically center image and text */
        }

        .navbar-brand-image {
            height: 30px;
            /* Adjust as needed */
            margin-right: 10px;
            /* Space between image and text */
        }

        .navbar-toggler {
            margin-left: auto;
            /* Push the toggler to the right */
        }

        .navbar-collapse {
            justify-content: flex-end;
            /* Align navbar items to the right */
        }

        /* Optional: Adjust margins or padding as needed */
        .navbar-nav {
            margin-left: auto;
            /* Or margin-right: 0 to remove default right margin */
        }

        .navbar-nav .nav-item {
            margin-left: 10px;
            /* Space between nav items */
        }

        body {
            padding-top: 70px;
            /* Adjust value if your navbar height is different */
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <img src="../assets/images/icon.jpg" class="navbar-brand-image">
            <a class="navbar-brand" href="#">Crime Management</a>

            <!-- Navbar Toggle Button (For Mobile View) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contacts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Helpline</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="start/signup.php">Signup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="start/login.php">Login</a>
                    </li>


                    <!-- Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Archives</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Push content down so it doesn't hide behind navbar -->
    <div style="margin-top: 56px;"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>