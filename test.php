<?php
require 'config.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test query
    $stmt = $pdo->query("SELECT current_database(), current_user, version()");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<p style='color: green;'>✅ Connected to database successfully!</p>";
    echo "<ul>";
    echo "<li><strong>Database:</strong> " . htmlspecialchars($result['current_database']) . "</li>";
    echo "<li><strong>User:</strong> " . htmlspecialchars($result['current_user']) . "</li>";
    echo "<li><strong>PostgreSQL Version:</strong> " . htmlspecialchars($result['version']) . "</li>";
    echo "</ul>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

