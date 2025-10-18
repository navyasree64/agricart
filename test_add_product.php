<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include('db.php');

try {
    // Get the first category
    $category_result = $conn->query("SELECT id FROM categories LIMIT 1");
    if ($category_result->num_rows == 0) {
        throw new Exception("No categories found in database. Please run test_db.php first to create categories.");
    }
    
    $category = $category_result->fetch_assoc();
    $category_id = $category['id'];
    
    // Test product data
    $name = "Test Product";
    $description = "This is a test product";
    $price = 100.00;
    $stock = 10;
    $image = "test.jpg";
    
    // Insert test product
    $sql = "INSERT INTO products (name, description, price, category_id, stock, image) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("ssdiis", $name, $description, $price, $category_id, $stock, $image);
    
    if ($stmt->execute()) {
        echo "Product added successfully!<br>";
        echo "New product ID: " . $stmt->insert_id . "<br>";
    } else {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    // Verify the product was added
    $verify = $conn->prepare("SELECT p.*, c.name as category_name 
                            FROM products p 
                            LEFT JOIN categories c ON p.category_id = c.id 
                            WHERE p.name = ?");
    $verify->bind_param("s", $name);
    $verify->execute();
    $result = $verify->get_result();
    
    if ($result->num_rows > 0) {
        echo "Product found in database:<br>";
        while ($row = $result->fetch_assoc()) {
            echo "ID: " . $row['id'] . "<br>";
            echo "Name: " . $row['name'] . "<br>";
            echo "Price: " . $row['price'] . "<br>";
            echo "Category: " . $row['category_name'] . "<br>";
            echo "Stock: " . $row['stock'] . "<br>";
        }
    } else {
        echo "Product not found in database!<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?> 