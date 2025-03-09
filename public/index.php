<!DOCTYPE html>
<html>
<head>
<title>PHP File Access</title>
<style>
  .php-file {
    font-weight: bold;
  }
  .folder-heading {
    font-size: 1.2em;
    margin-top: 1em;
  }
  .folder-list { /* Style for the lists within folders */
    list-style-type: none; /* Remove bullet points */
    padding-left: 20px; /* Indent the list */
  }
</style>
</head>
<body>

<h1>PHP Files in C:\xampp\htdocs\crime-record-system\public</h1>

<?php

$directory = 'C:\xampp\htdocs\crime-record-system\public';
$baseUrl = 'http://localhost/crime-record-system/public/';

function displayFiles($dir, $baseUrl) { // Recursive function
    $files = scandir($dir);
    if ($files === false) {
      echo "Error: Could not open directory " . $dir . "<br>";
      return; // Stop processing this directory
    }
    $hasFiles = false; // Flag to check if any files were found in this dir or subdirs
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            $path = $dir . "/" . $file;
            if (is_dir($path)) {
                echo "<h2 class='folder-heading'>" . $file . "</h2>";
                echo "<ul class='folder-list'>";
                $hasSubFiles = displayFiles($path, $baseUrl); // Recursive call
                echo "</ul>";
                if ($hasSubFiles) $hasFiles = true; // Set if any subfiles were found
            } else if (pathinfo($path, PATHINFO_EXTENSION) == 'php') {
                $relativePath = str_replace($GLOBALS['directory'], '', $path); // Use global $directory
                $url = $baseUrl . ltrim($relativePath, '/\\');
                if (!$hasFiles) { // Create the <ul> only if files are found
                    echo "<ul class='folder-list'>";
                    $hasFiles = true;
                }
                echo "<li><a href='" . $url . "' target='_blank' class='php-file'>" . $file . "</a></li>";
            }
        }
    }
    return $hasFiles; // Return true if any files were found, false otherwise
}


echo "<ul class='folder-list'>"; // Start the initial list
displayFiles($directory, $baseUrl); // Call the recursive function
echo "</ul>";

?>

</body>
</html>