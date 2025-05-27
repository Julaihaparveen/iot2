<?php
// config.php

$host = 'localhost';
$db   = 'IOT2';
$user = 'postgres';
$pass = 'root';
$port = '5432'; // default port for PostgreSQL

$dsn = "pgsql:host=$host;port=$port;dbname=$db";

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass);

    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection errors
    die("⚠️ Database connection failed: " . $e->getMessage());
}
