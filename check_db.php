<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
require_once __DIR__ . '/config.php';
$host = DB_HOST;
$dbname = DB_NAME;
$username = DB_USER;
$password = DB_PASS;

try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "Database connection successful!<br>";
    
    // Check if products table exists
    $result = $conn->query("SHOW TABLES LIKE 'products'");
    if ($result->num_rows == 0) {
        echo "Products table does not exist!<br>";
    } else {
        echo "Products table exists.<br>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE products");
        echo "<br>Products table structure:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "Field: " . $row['Field'] . " | Type: " . $row['Type'] . "<br>";
        }
    }
    
    // Check if categories table exists
    $result = $conn->query("SHOW TABLES LIKE 'categories'");
    if ($result->num_rows == 0) {
        echo "Categories table does not exist!<br>";
    } else {
        echo "Categories table exists.<br>";
        
        // Show categories
        $result = $conn->query("SELECT * FROM categories");
        echo "<br>Categories:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " | Name: " . $row['name'] . "<br>";
        }
    }
    
    // Show all products
    $result = $conn->query("SELECT * FROM products");
    echo "<br>Products in database:<br>";
    if ($result->num_rows == 0) {
        echo "No products found in database.<br>";
    } else {
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Category ID: " . $row['category_id'] . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 