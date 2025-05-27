<?php
include 'config.php';
 
echo "<h2>PostgreSQL Connection Test</h2>";
 
if ($conn) {
    echo "<p style='color: green;'>✅ Connected successfully to database <strong>IOT2</strong>.</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to connect to database.</p>";
}
?>