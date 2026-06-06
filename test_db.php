<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';
$host = DB_HOST;
$username = DB_USER;
$password = DB_PASS;

try {
    // First, connect without selecting a database
    $conn = new PDO("mysql:host=$host", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection Test</h2>";
    echo "<p>Successfully connected to MySQL server!</p>";
    
    // List all databases
    $stmt = $conn->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Available Databases:</h3>";
    echo "<ul>";
    foreach ($databases as $db) {
        echo "<li>" . htmlspecialchars($db) . "</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 