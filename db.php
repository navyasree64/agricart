<?php
/**
 * Database Connection Configuration
 * 
 * This file handles the connection to the MySQL database.
 * It implements error handling and configurable connection parameters.
 */

// Database credentials - consider moving these to a separate config file for better security
$servername = "localhost";
$username = "root";
$password = "";
$database = "agri_ecommerce";
$port = 3306; // Default MySQL port

// Error reporting settings
// Note: In production, you may want to disable error display and only log errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establish connection - using try/catch for better error handling
try {
    // Create connection with error reporting mode
    $conn = new mysqli($servername, $username, $password, $database, $port);
    
    // Set charset to ensure proper handling of special characters
    $conn->set_charset("utf8mb4");
    
    // Check connection directly
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Optionally, configure some MySQL settings
    $conn->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    
} catch (Exception $e) {
    // Log the error to a file (recommended for production)
    error_log("Database connection error: " . $e->getMessage(), 0);
    
    // Display user-friendly error (customize as needed)
    die("We're experiencing technical difficulties. Please try again later.");
}

/**
 * Helper function for safe query execution
 * 
 * @param string $sql SQL query with placeholders
 * @param string $types Types of parameters (i: integer, d: double, s: string, b: blob)
 * @param array $params Array of parameters to bind
 * @return mysqli_stmt|false Returns the prepared statement or false on failure
 */
if (!function_exists('executeQuery')) {
    function executeQuery($sql, $types = "", $params = []) {
        global $conn;
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            error_log("Query preparation failed: " . $conn->error);
            return false;
        }
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        if (!$stmt->execute()) {
            error_log("Query execution failed: " . $stmt->error);
            return false;
        }
        
        return $stmt;
    }
}

// Enable this for automatic cleanup when script ends (optional)
// register_shutdown_function(function() {
//     global $conn;
//     if ($conn) {
//         $conn->close();
//     }
// });
?>

