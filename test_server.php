<?php
// Simple server test - upload this to htdocs and visit it
echo "<h2>✅ PHP is working!</h2>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Host: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>Time: " . date('Y-m-d H:i:s') . "</p>";

// Test database connection
define('DB_HOST', 'sql207.infinityfree.com');
define('DB_USER', 'if0_42108185');
define('DB_PASS', 'jpSlFNwdGePrqE');
define('DB_NAME', 'if0_42108185_agricart');

$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn) {
    echo "<p style='color:green'>✅ Database connected successfully!</p>";
    
    // Check if tables exist
    $result = $conn->query("SHOW TABLES");
    echo "<p>Tables found: " . $result->num_rows . "</p>";
    echo "<ul>";
    while ($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
    $conn->close();
} else {
    echo "<p style='color:red'>❌ Database connection failed: " . mysqli_connect_error() . "</p>";
}
?>
