<?php
$host = 'localhost'; // Database host
$user = 'root'; // Database username
$password = ''; // Database password
$db_name = 'gestion_cv'; // Replace with your database name

// Create a connection to the database
$conn = mysqli_connect($host, $user, $password, $db_name);

// Check the connetion
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
