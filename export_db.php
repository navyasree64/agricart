<?php
/**
 * Database Export Helper Script
 * Exports the local MySQL database to a SQL file without requiring XAMPP or phpMyAdmin.
 */

include('db.php');

// Set headers for file download or console output
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain');
}

$tables = array();
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_row()) {
    $tables[] = $row[0];
}

$sql_dump = "-- AgriCart Database Export\n";
$sql_dump .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
$sql_dump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    // Get table structure
    $result = $conn->query("SHOW CREATE TABLE `$table`");
    $row = $result->fetch_row();
    $sql_dump .= "\n\n" . $row[1] . ";\n\n";
    
    // Get table data
    $result = $conn->query("SELECT * FROM `$table`");
    $num_fields = $result->field_count;
    
    while ($row = $result->fetch_row()) {
        $sql_dump .= "INSERT INTO `$table` VALUES(";
        for ($j = 0; $j < $num_fields; $j++) {
            if (isset($row[$j])) {
                // Escape characters
                $val = $conn->real_escape_string($row[$j]);
                $sql_dump .= '"' . $val . '"';
            } else {
                $sql_dump .= 'NULL';
            }
            if ($j < ($num_fields - 1)) {
                $sql_dump .= ',';
            }
        }
        $sql_dump .= ");\n";
    }
}

$sql_dump .= "\nSET FOREIGN_KEY_CHECKS=1;\n";

// Save to file
$filename = 'db_export.sql';
if (file_put_contents($filename, $sql_dump)) {
    echo "✅ Success: Database exported successfully to '$filename'\n";
    echo "📁 You can now find the file inside your project folder and upload it directly to InfinityFree phpMyAdmin.\n";
} else {
    echo "❌ Error: Failed to write to '$filename'\n";
}
?>
