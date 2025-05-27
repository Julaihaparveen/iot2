<?php
// Database configuration
$host = "localhost";
$port = "5432";
$dbname = "IOT2";
$user = "database";
$password = "root";
 
// Create connection string
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
 
// Check connection
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
?>